<?php

namespace Dusk\UserBundle\Controller;

use Dusk\UserBundle\Entity\Room;
use Dusk\UserBundle\Entity\SubscriptionRepository;
use Dusk\UserBundle\Form\Type\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;
use Dusk\UserBundle\Entity\Order;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use \DateTime;

class RoomController extends Controller {
	
	/**
     * Index action for room list
     * @id integer roomID
     */
    public function indexAction($id) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $venue = $em->getRepository('DuskUserBundle:Venue')->find($id);
        $rooms = $em->getRepository('DuskUserBundle:Room')->findBy(array('venue' => $id));
        $venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId()));

        $activeRoom = 0;
        $inactiveRoom = 0;

        foreach ($rooms as $room) {
            if ($room->isActiveRoom())
                $activeRoom += 1;
            else
                $inactiveRoom += 1;
        }
        return $this->render('DuskUserBundle:Room:index.html.twig', array(
                    'venue' => $venue,
                    'rooms' => $rooms,
                    'active' => $activeRoom,
                    'inactive' => $inactiveRoom,
                    'venues' => $venues,
                    'venueCount' => count($venues),
        ));
    }
	
	/**
     * New action for new room book
     * @id integer roomID
     */
    public function newAction($id) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId()));

        $activeVenue = 0;
        $inactiveVenue = 0;
        foreach ($venues as $venue) {
            if ($venue->getIsActive())
                $activeVenue += 1;
            else
                $inactiveVenue += 1;
        }

        $objVenue = $em->getRepository('DuskUserBundle:Venue')->find($id);

        if ($user->getIsFreePeriod()) {

            $countRoom = 0;
            foreach ($objVenue->getRooms() as $room) {
                $countRoom++;
            }

            if ($countRoom >= 1) {
                $this->get('session')->getFlashBag()->add('notice', 'You are allowed to add one room only.');
                return $this->redirect($this->generateUrl('dusk_room', array('id' => $objVenue->getId())));
            }
        }


        $form = $this->createFormBuilder()
                ->add('venue', 'hidden', array('data' => $id))
                ->add('name', 'text')
                ->add('subscription', 'entity', array(
                    'class' => 'DuskUserBundle:Subscription',
                    'property' => 'name',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('e')
                        ->add('orderBy', 'e.price ASC');
            }, 'label' => 'Your Current Subscription', 'multiple' => false, 'expanded' => true, 'required' => true
                ))
                ->add('username', 'email', array('required' => true))
                ->add('password', 'password', array('required' => true))
                ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {

            $form->bind($request);
            $data = $form->getData();

            $validator = $this->container->get('validator');
            $foundUser = $em->getRepository('DuskUserBundle:User')->findBy(array('email' => $data['username']));
            if ($foundUser) {
                $error = new formerror('Email already registered.');
                $form->get('username')->addError($error);
            }

            if ($form->isValid()) {
                // code for free user
                if ($user->getIsFreePeriod()) {

                    $countRoom = 0;
                    foreach ($objVenue->getRooms() as $room) {
                        $countRoom++;
                    }

                    if ($countRoom >= 1) {
                        $this->get('session')->getFlashBag()->add('notice', 'You are allowed to add one room only.');
                        return $this->redirect($this->generateUrl('dusk_room'));
                    }

                    $startedAt = new \DateTime();
                    $expiredAt = new \DateTime();
                    $period = "+14 day";
                    $expiredAt->modify($period);

                    $userManager = $this->get('fos_user.user_manager');
                    $objUser = $userManager->createUser();
                    $objUser->setPlainPassword($data['password']);
                    $objUser->setUsername($data['username']);
                    $objUser->setUsernameCanonical($data['username']);
                    $objUser->setEmail($data['username']);
                    $objUser->setEmailCanonical($data['username']);
                    $objUser->setCountry($user->getCountry());
                    $objUser->setState($user->getState());
                    $objUser->setEnabled(1);
                    $objUser->setLocked(0);
                    $objUser->setExpiresAt($expiredAt);
                    $objUser->setCredentialsExpired(0);
                    $objUser->setCredentialsExpireAt($expiredAt);
                    $objUser->setRoles(array('ROLE_USER'));
                    $userManager->updateUser($objUser);

                    $subscription = $data['subscription'];
                    $date = new \DateTime();
                    $orderNumber = $date->getTimestamp();

                    $order = new Order($subscription->getPrice(), $orderNumber);
                    $order->setAmount($subscription->getPrice());
                    $em->persist($order);
                    $em->flush();

                    $objRoom = new Room();
                    $objRoom->setAdmin($user);
                    $objRoom->setVenue($objVenue);
                    $objRoom->setUser($objUser);
                    $objRoom->setSubscription($data['subscription']);
                    $objRoom->setName($data['name']);
                    $objRoom->setIsActive(1);
                    $objRoom->setPaymentStatus('unpaid');
                    $objRoom->setStartedAt($startedAt);
                    $objRoom->setExpiredAt($expiredAt);
                    $objRoom->setOrder($order);
                    $em->persist($objRoom);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('notice', 'Your new free Room has been added to your Venue.');

                    return $this->redirect($this->generateUrl('dusk_room', array('id' => $objVenue->getId())));
                }

                // code for paypal checkout

                $startedAt = new \DateTime();
                $expiredAt = new \DateTime();
                $period = "+" . $data['subscription']->getMonth() . " month";
                $expiredAt->modify($period);

                $userManager = $this->get('fos_user.user_manager');
                $objUser = $userManager->createUser();
                $objUser->setPlainPassword($data['password']);
                $objUser->setUsername($data['username']);
                $objUser->setUsernameCanonical($data['username']);
                $objUser->setEmail($data['username']);
                $objUser->setEmailCanonical($data['username']);
                $objUser->setCountry($user->getCountry());
                $objUser->setState($user->getState());
                $objUser->setEnabled(0);
                $objUser->setLocked(1);
                $objUser->setExpiresAt($expiredAt);
                $objUser->setCredentialsExpired(0);
                $objUser->setCredentialsExpireAt($expiredAt);
                $objUser->setRoles(array('ROLE_USER'));
                $userManager->updateUser($objUser);

                $subscription = $data['subscription'];
                $date = new \DateTime();
                $orderNumber = $date->getTimestamp();

                $order = new Order($subscription->getPrice(), $orderNumber);
                $order->setAmount($subscription->getPrice());
                $em->persist($order);
                $em->flush();

                $objRoom = new Room();
                $objRoom->setAdmin($user);
                $objRoom->setVenue($objVenue);
                $objRoom->setUser($objUser);
                $objRoom->setSubscription($data['subscription']);
                $objRoom->setName($data['name']);
                $objRoom->setIsActive(0);
                $objRoom->setPaymentStatus('unpaid');
                $objRoom->setStartedAt($startedAt);
                $objRoom->setExpiredAt($expiredAt);
                $objRoom->setOrder($order);
                $em->persist($objRoom);
                $em->flush();

                return $this->redirect($this->generateUrl('payment_checkout', array('orderNumber' => $orderNumber)));
            }
        }
        return $this->render('DuskUserBundle:Room:new.html.twig', array(
                    'form' => $form->createView(),
                    'venue' => $objVenue
        ));
    }

	/**
     * Detail action for room detail
     * @id integer roomID
	 * @return room array
     */
    public function detailAction($id) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $objRoom = $em->getRepository('DuskUserBundle:Room')->find($id);

        $datetime = new DateTime('now');
        $form = $this->createFormBuilder()
                ->add('room', 'hidden', array('data' => $id))
                ->add('name', 'text', array('data' => $objRoom->getName()))
                ->add('subscription', 'entity', array('disabled' => $objRoom->getExpiredAt()->format('Y-m-d') > $datetime->format('Y-m-d') ? true : false,
                    'class' => 'DuskUserBundle:Subscription',
                    'property' => 'name',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('e')
                        ->add('orderBy', 'e.price ASC');
            }, 'label' => 'Your Current Subscription', 'multiple' => false, 'expanded' => true, 'required' => true, 'data' => $objRoom->getSubscription()
                ))
                ->add('username', 'email', array('required' => true, 'data' => $objRoom->getUser()->getUsername()))
                ->add('password', 'password', array('required' => true))
                ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {

            $form->bind($request);

            if ($form->isValid()) {
                $data = $form->getData();

                if ($user->getIsFreePeriod()) {
                    $startedAt = new \DateTime();
                    $expiredAt = new \DateTime();
                    $period = "+14 day";
                    $expiredAt->modify($period);
                } else {
                    $startedAt = new \DateTime();
                    $expiredAt = new \DateTime();
                }
                
                $objUser = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($data['username']);
                $userManager = $this->get('fos_user.user_manager');

                if (!$objUser) {
                    $objUser = $userManager->createUser();
                    $objUser->setUsername($data['username']);
                    $objUser->setUsernameCanonical($data['username']);
                    $objUser->setEmail($data['username']);
                    $objUser->setEmailCanonical($data['username']);
                    $objUser->setCountry($user->getCountry());
                    $objUser->setState($user->getState());
                    $objUser->setEnabled(1);
                    $objUser->setLocked(0);
                    $objUser->setExpiresAt($objRoom->getExpiredAt());
                    $objUser->setCredentialsExpired(0);
                    $objUser->setCredentialsExpireAt($objRoom->getExpiredAt());
                    $objUser->setRoles(array('ROLE_USER'));
                }

                $objUser->setPlainPassword($data['password']);
                $userManager->updateUser($objUser);

                $objRoom->setUser($objUser);
                $objRoom->setName($data['name']);

                $em->persist($objRoom);
                $em->flush();

                if (isset($data['subscription'])) {
                    $subscription = $data['subscription'];

                    $objRoom->setIsActive(0);
                    $em->persist($objRoom);
                    $em->flush();

                    if ($user->getIsFreePeriod()) {
                        $startedAt = new \DateTime();
                        $expiredAt = new \DateTime();
                        $period = "+14 day";
                        $expiredAt->modify($period);
                    } else {
                        $period = "+" . $data['subscription']->getMonth() . " month";
                        $expiredAt->modify($period);
                    }
                    $date = new \DateTime();
                    $orderNumber = $date->getTimestamp();

                    $order = new Order($subscription->getPrice(), $orderNumber);
                    $order->setAmount($subscription->getPrice());
                    $em->persist($order);
                    $em->flush();

                    $objRoom = new Room();
                    $objRoom->setAdmin($user);
                    $objRoom->setVenue($objRoom->getVenue());
                    $objRoom->setUser($objUser);
                    $objRoom->setSubscription($data['subscription']);
                    $objRoom->setName($data['name']);
                    $objRoom->setIsActive(0);
                    $objRoom->setPaymentStatus('unpaid');
                    $objRoom->setStartedAt($startedAt);
                    $objRoom->setExpiredAt($expiredAt);
                    $objRoom->setOrder($order);
                    $em->persist($objRoom);
                    $em->flush();

                    if ($user->getIsFreePeriod()) {
                        $this->get('session')->getFlashBag()->add('notice', 'Your free Room has been updated.');
                        return $this->redirect($this->generateUrl('dusk_room', array('id' => $objRoom->getVenue()->getId())));
                    }

                    return $this->redirect($this->generateUrl('payment_checkout', array('orderNumber' => $orderNumber)));
                }
                if ($user->getIsFreePeriod()) {
                    $this->get('session')->getFlashBag()->add('notice', 'Your free Room has been updated.');
                    return $this->redirect($this->generateUrl('dusk_room', array('id' => $objRoom->getVenue()->getId())));
                }
                $this->get('session')->getFlashBag()->add('notice', 'Your Room has been updated.');
            }
        }

        return $this->render('DuskUserBundle:Room:detail.html.twig', array(
                    'form' => $form->createView(),
                    'room' => $objRoom,
        ));
    }
	
	/**
     * Update action for room
     * @id integer roomID
     */
    public function updateAction($id) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $objRoom = $em->getRepository('DuskUserBundle:Room')->find($id);

        $form = $this->createFormBuilder()
                ->add('venue', 'hidden', array('data' => $objRoom->getVenue()->getId()))
                ->add('name', 'text')
                ->add('subscription', 'entity', array(
                    'class' => 'DuskUserBundle:Subscription',
                    'property' => 'name',
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('e')
                        ->add('orderBy', 'e.price ASC');
            }, 'label' => 'Your Current Subscription', 'multiple' => false, 'expanded' => true, 'required' => true
                ))
                ->add('username', 'text', array('required' => true))
                ->add('password', 'password', array('required' => true))
                ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {

            $form->bind($request);

            if ($form->isValid()) {

                $data = $form->getData();

                $startedAt = new \DateTime();
                $expiredAt = new \DateTime();

                $subscription = $data['subscription'];

                $objUser = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($data['username']);
                $userManager = $this->get('fos_user.user_manager');

                if (!$objUser) {
                    $objUser = $userManager->createUser();
                    $objUser->setUsername($data['username']);
                    $objUser->setUsernameCanonical($data['username']);
                    $objUser->setEmail($data['username']);
                    $objUser->setEmailCanonical($data['username']);
                    $objUser->setCountry($user->getCountry());
                    $objUser->setState($user->getState());
                    $objUser->setEnabled(1);
                    $objUser->setLocked(0);
                    $objUser->setExpiresAt($objRoom->getExpiredAt());
                    $objUser->setCredentialsExpired(0);
                    $objUser->setCredentialsExpireAt($objRoom->getExpiredAt());
                    $objUser->setRoles(array('ROLE_USER'));
                }

                $objUser->setPlainPassword($data['password']);
                $userManager->updateUser($objUser);

                $objRoom->setUser($objUser);
                $objRoom->setName($data['name']);

                $em->persist($objRoom);
                $em->flush();

                if ($subscription) {

                    $date = new \DateTime();
                    $orderNumber = $date->getTimestamp();

                    $order = new Order($subscription->getPrice(), $orderNumber);
                    $order->setAmount($subscription->getPrice());
                    $em->persist($order);
                    $em->flush();

                    $period = "+" . $data['subscription']->getMonth() . " month";
                    $expiredAt->modify($period);

                    $objRoom = new Room();
                    $objRoom->setAdmin($user);
                    $objRoom->setVenue($objVenue);
                    $objRoom->setUser($objUser);
                    $objRoom->setSubscription($data['subscription']);
                    $objRoom->setName($data['name']);
                    $objRoom->setIsActive(0);
                    $objRoom->setPaymentStatus('unpaid');
                    $objRoom->setStartedAt($startedAt);
                    $objRoom->setExpiredAt($expiredAt);
                    $objRoom->setOrder($order);
                    $em->persist($objRoom);
                    $em->flush();

                    return $this->redirect($this->generateUrl('payment_checkout', array('orderNumber' => $orderNumber)));
                }
            }
        }
        return $this->redirect($this->generateUrl('dusk_account'));
    }
	
	/**
     * Change activation action for room activation
     * @id integer roomID
     */
    public function changeActivationAction($id) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $objRoom = $em->getRepository('DuskUserBundle:Room')->find($id);
        if ($objRoom->getAdmin()->getId() == $user->getId()) {
            $objRoom->setIsActive($objRoom->getIsActive() ? false : true);
            $em->persist($objRoom);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('dusk_account'));
    }
	
	/**
     * Remove action for room
     * @id integer roomID
     */
    public function removeAction($id) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();


        $objRoom = $em->getRepository('DuskUserBundle:Room')->createQueryBuilder('r')
                        ->where('r.id = :id')
                        ->andWhere('r.admin = :admin OR r.user = :user')
                        ->setParameter('id', $id)
                        ->setParameter('admin', $user->getId())
                        ->setParameter('user', $user->getId())
                        ->getQuery()->getOneOrNullResult();
        if ($objRoom) {
            $objRoom->setIsActive(0);
            $em->persist($objRoom);
            $em->flush();
        }
        $referer = $this->getRequest()->headers->get("referer");
        return $this->redirect($referer);
    }
	
	/**
     * Purchase action for room
     * @orderNumber integer ordernumber of new order
     */
    public function purchaseAction($orderNumber) {

        $em = $this->getDoctrine()->getManager();
        $objOrder = $em->getRepository('DuskUserBundle:Order')->findOneBy(array('orderNumber' => $orderNumber));
        if ($objOrder) {
            $objRoom = $em->getRepository('DuskUserBundle:Room')->findOneBy(array('order' => $objOrder->getId()));
            $objRoom->setPaymentStatus(true);
            $objRoom->setIsActive(1);

            $objRoom->getUser()->setEnabled(1);
            $objRoom->getUser()->setLocked(0);

            $em->persist($objRoom);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Payment was successful. Your new Room has been added to your Venue.');
        }

        return $this->redirect($this->generateUrl('dusk_billing_history'));

//        $user = $this->container->get('security.context')->getToken()->getUser();
//        $objRoom = $em->getRepository('DuskUserBundle:Room')->find($objRoom->getId());
//
//        $form = $this->createFormBuilder()
//                ->add('room', 'hidden', array('data' => $objRoom->getId()))
//                ->add('name', 'text', array('data' => $objRoom->getName()))
//                ->add('subscription', 'entity', array(
//                    'class' => 'DuskUserBundle:Subscription',
//                    'property' => 'name',
//                    'query_builder' => function(EntityRepository $er) {
//                return $er->createQueryBuilder('e')
//                        ->add('orderBy', 'e.price ASC');
//            }, 'label' => 'Your Current Subscription', 'multiple' => false, 'expanded' => true, 'required' => true, 'data' => $objRoom->getSubscription()
//                ))
//                ->add('username', 'text', array('required' => true, 'data' => $objRoom->getUser()->getUsername()))
//                ->add('password', 'password', array('required' => true))
//                ->getForm();
//        return $this->render('DuskUserBundle:Room:detail.html.twig', array(
//                    'form' => $form->createView(),
//                    'room' => $objRoom
//        ));
    }

}
