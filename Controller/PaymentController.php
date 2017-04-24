<?php

namespace Dusk\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Payment\CoreBundle\PluginController\Result;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use JMS\Payment\CoreBundle\Entity\ExtendedData;
use Dusk\UserBundle\Entity\Order;
use Doctrine\ORM\EntityRepository;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;

/**
 * @Route("/payments")
 */
class PaymentController extends Controller {

    /** @DI\Inject */
    private $request;

    /** @DI\Inject */
    private $router;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $em;

    /** @DI\Inject("payment.plugin_controller") */
    private $ppc;

    /**
     * @Route("/{orderNumber}/details", name = "payment_details")
     * @Template
     */
    public function detailsAction(Order $order) {

        $checkout_params = $this->generateCheckoutParameters($order);

        $form = $this->getFormFactory()->create('jms_choose_payment_method', null, array(
            'amount' => $order->getAmount(),
            'currency' => 'USD',
            'default_method' => 'payment_paypal',
            'predefined_data' => array(
                'checkout_params' => $checkout_params,
                'paypal_express_checkout' => array(
                    'return_url' => $this->router->generate('payment_complete', array(
                        'orderNumber' => $order->getOrderNumber(),
                            ), true),
                    'cancel_url' => $this->router->generate('payment_cancel', array(
                        'orderNumber' => $order->getOrderNumber(),
                            ), true),
                ),
            ),
        ));

        if ('POST' === $this->request->getMethod()) {
            $form->bind($this->request);

            if ($form->isValid()) {

                $this->ppc->createPaymentInstruction($instruction = $form->getData());

                $order->setPaymentInstruction($instruction);
                $this->em->persist($order);
                $this->em->flush($order);

                return new RedirectResponse($this->router->generate('payment_complete', array(
                            'orderNumber' => $order->getOrderNumber(),
                )));
            }
        }

        return $this->render('DuskUserBundle:Payment:details.html.twig', array('form' => $form->createView(), 'orderNumber' => $order->getOrderNumber()));
    }

    /**
     * @Route("/{orderNumber}/complete", name = "payment_complete")
     */
    public function completeAction(Order $order) {
        $instruction = $order->getPaymentInstruction();

        if (null === $pendingTransaction = $instruction->getPendingTransaction()) {
            $payment = $this->ppc->createPayment($instruction->getId(), $instruction->getAmount() - $instruction->getDepositedAmount());
        } else {
            $payment = $pendingTransaction->getPayment();
        }

        $result = $this->ppc->approveAndDeposit($payment->getId(), $payment->getTargetAmount());
        if (Result::STATUS_PENDING === $result->getStatus()) {
            $ex = $result->getPluginException();

            if ($ex instanceof ActionRequiredException) {
                $action = $ex->getAction();

                if ($action instanceof VisitUrl) {
                    return new RedirectResponse($action->getUrl());
                }

                throw $ex;
            }
        } else if (Result::STATUS_SUCCESS !== $result->getStatus()) {
            $em = $this->getDoctrine()->getManager();

            $objRoom = $em->getRepository('DuskUserBundle:Room')->findOneBy(array('order' => $order->getId()));
            $venueId = $objRoom->getVenue()->getId();
            $objUser = $em->getRepository('DuskUserBundle:User')->findOneBy(array('id' => $objRoom->getUser()->getId()));

            $em->remove($order);
            $em->remove($objUser);
            $em->remove($objRoom);

            $em->flush();
            throw new \RuntimeException('Transaction was not successful: ' . $result->getReasonCode());
        }

        $em = $this->getDoctrine()->getManager();

        if ($order) {
            $objRoom = $em->getRepository('DuskUserBundle:Room')->findOneBy(array('order' => $order->getId()));
            $objRoom->setPaymentStatus(true);
            $objRoom->setIsActive(1);

            $objRoom->getUser()->setEnabled(1);
            $objRoom->getUser()->setLocked(0);

            $em->persist($objRoom);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Payment was successful. Your new Room has been added to your Venue.');
            
            $data = array();
            $data['name'] = $objRoom->getAdmin()->getUsername();
            $data['room'] = $objRoom->getName();
            $data['subname'] = $objRoom->getSubscription()->getName();
            $data['subprice'] = $objRoom->getSubscription()->getPrice();
            $data['submonth'] = $objRoom->getSubscription()->getMonth();
            $data['subdate'] = $objRoom->getStartedAt();

            $message = \Swift_Message::newInstance()
                    ->setSubject('Dusk Payment Successful')
                    ->setFrom('contact@dusk.com')
                    ->setTo($objRoom->getAdmin()->getEmail())
                    ->setBody($this->renderView('DuskUserBundle:Email:payment.txt.twig', array('data' => $data)))
                    ->setContentType('text/html');
            $this->get('mailer')->send($message);
        }

        return $this->redirect($this->generateUrl('dusk_billing_history'));

//        return new RedirectResponse($this->router->generate('room_purchase', array('orderNumber' => $order->getOrderNumber(),)));
        // payment was successful, do something interesting with the order
    }

    /**
     * @Route("/{orderNumber}/cancel", name = "payment_complete")
     */
    public function cancelAction(Order $order) {

        $this->get('session')->getFlashBag()->add('info', 'Transaction / Payment Cancelled.');

        $em = $this->getDoctrine()->getManager();

        $objRoom = $em->getRepository('DuskUserBundle:Room')->findOneBy(array('order' => $order->getId()));
        $venueId = $objRoom->getVenue()->getId();
        $objUser = $em->getRepository('DuskUserBundle:User')->findOneBy(array('id' => $objRoom->getUser()->getId()));

        $em->remove($order);
        $em->remove($objUser);
        $em->remove($objRoom);

        $em->flush();

        return $this->redirect($this->generateUrl('dusk_room_new', array('id' => $venueId)));
    }

    /** @DI\LookupMethod("form.factory") */
    protected function getFormFactory() {
        
    }

    /**
     * Paypal Express Checkout
     *
     * @Route("/{orderNumber}/checkout", name="payment_checkout")
     * @Template
     */
    public function checkoutAction(Order $order) {
        $em = $this->get('doctrine.orm.entity_manager');
        $router = $this->get('router');

        // Create the extended data object
        $extendedData = new ExtendedData();

        // Complete payment return URL
        $extendedData->set('return_url', $router->generate('payment_complete', array(
                    'orderNumber' => $order->getOrderNumber(),
                        ), true)
        );

        // Cancel payment return URL
        $extendedData->set('cancel_url', $router->generate('payment_cancel', array(
                    'orderNumber' => $order->getOrderNumber(),
                        ), true)
        );

        // Checkout parameters
        $checkout_params = $this->generateCheckoutParameters($order);

        $this->get('logger')->info(print_r($checkout_params, 1));

        // Add checkout information to the exended data
        $extendedData->set('checkout_params', $checkout_params);

        // Create the payment instruction object
        $instruction = new PaymentInstruction(
                $order->getAmount(), 'USD', 'paypal_express_checkout', $extendedData
        );

        // Validate and persist the payment instruction
        $this->get('payment.plugin_controller')->createPaymentInstruction($instruction);

        // Update the order object
        $order->setPaymentInstruction($instruction);
        $em->persist($order);
        $em->flush();

        // Continue with payment
        return new RedirectResponse($router->generate('payment_complete', array(
                    'orderNumber' => $order->getOrderNumber(),
        )));
    }

    protected function generateCheckoutParameters(Order $order) {
        // Checkout parameters
        $checkout_params = array();

        // Include items data in the order
        $em = $this->getDoctrine()->getManager();

        $objRoom = $em->getRepository('DuskUserBundle:Room')->findOneBy(array('order' => $order->getId()));

        $checkout_params = array(
            'PAYMENTREQUEST_0_NAME' => $objRoom->getName(),
            'PAYMENTREQUEST_0_DESC' => "Creating Room '" . $objRoom->getName() . "' for Dusk \n Amount $" . $objRoom->getSubscription()->getPrice(),
            'PAYMENTREQUEST_0_AMT' => "$" . $objRoom->getSubscription()->getPrice(),
            'PAYMENTREQUEST_0_QTY' => 1,
            'PAYMENTREQUEST_0_INVNUM' => $order->getOrderNumber(),
        );

        return $checkout_params;
    }

}
