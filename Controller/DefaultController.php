<?php

namespace Dusk\UserBundle\Controller;

use Dusk\UserBundle\Entity\Newsletter;
use Dusk\UserBundle\Entity\Enquiry;
use Dusk\UserBundle\Form\Type\ContactFormType;
use Dusk\UserBundle\Form\Type\NewsletterType;
use Dusk\UserBundle\Form\Type\InviteFriendType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dusk\UserBundle\Entity\InviteFriend;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Email;
use \DateTime;

//use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {
	
	/**
     * Index action
    */
    public function indexAction() {
        return $this->render('DuskUserBundle:Default:index.html.twig', array());
    }
	
	/**
     * Banner action for get banner list
     * @return banner records
    */
    public function bannerAction() {
        $em = $this->getDoctrine()->getManager();
        $records = $em->getRepository("DuskUserBundle:Banner")
                        ->createQueryBuilder('b')
                        ->where('b.is_active = :active')
                        ->setParameter(':active', 1)
                        ->andWhere('b.category = :category')
                        ->setParameter('category', 1)
                        ->orderBy('b.order', 'ASC')
                        ->getQuery()->getResult();
        return $this->render('DuskUserBundle:Default:banner.html.twig', array('records' => $records));
    }
	
	/**
     * Inner banner action for banner
     * @return banner array
    */
    public function innerBannerAction() {
        $em = $this->getDoctrine()->getManager();
        $records = $em->getRepository("DuskUserBundle:Banner")
                        ->createQueryBuilder('b')
                        ->where('b.is_active = :active')
                        ->setParameter(':active', 1)
                        ->andWhere('b.category = :category')
                        ->setParameter('category', 2)
                        ->orderBy('b.order', 'ASC')
                        ->getQuery()->getResult();
        return $this->render('DuskUserBundle:Default:banner.html.twig', array('records' => $records));
    }
	
	/**
	 * Partner action for getting list of partners
	 * @return partners array
	*/
    public function partnersAction() {
        $em = $this->getDoctrine()->getManager();
        $records = $em->getRepository("DuskUserBundle:Partner")->createQueryBuilder('p')
                        ->where('p.is_active = :active')
                        ->setParameter(':active', 1)
                        ->getQuery()->getArrayResult();

        foreach ($records as $key => $record) {
            $partners[$key]['image'] = $record['image'];
            $partners[$key]['url'] = $record['link'];
        }
        return $this->render('DuskUserBundle:Default:partners.html.twig', array('partners' => $partners));
    }
	
	/**
	 * Contact action for contact form
	*/
    public function contactAction() {
        $enquiry = new Enquiry();
        $form = $this->createForm(new ContactFormType(), $enquiry);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {

            $form->bind($request);

            if ($form->isValid()) {
                $data = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->persist($enquiry);
                $em->flush();

                $message = \Swift_Message::newInstance()
                        ->setSubject('New Contact Enquiry')
                        ->setFrom($data->getEmail())
                        ->setTo('play@duskmusic.com')
                        ->setBody($this->renderView('DuskUserBundle:Email:contact.txt.twig', array('data' => $data)))
                        ->setContentType('text/html');
                $this->get('mailer')->send($message);

                $this->get('session')->getFlashBag()->add('notice', 'Your request has been submitted!');
                return $this->redirect($this->generateUrl('dusk_contactus'));
            }
        }
        return $this->render('DuskUserBundle:Default:contact.html.twig', array('form' => $form->createView()));
    }
	
	/**
	 * Left panel action for venue and route
	 * @route bool || string
	*/
    public function leftPanelAction($route) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $venues = null;
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $em = $this->getDoctrine()->getManager();
            $venues = $em->getRepository('DuskUserBundle:Venue')->createQueryBuilder('v')->select('COUNT(v)')->where('v.admin = :admin')->setParameter('admin', $user->getId())->getQuery()->getSingleScalarResult();
        }
        return $this->render('DuskUserBundle:Default:leftPanel.html.twig', array('venues' => $venues, 'route' => $route));
    }
	
	/**
	 * Save invite action for friends by mail and message
	 * @return json response
	*/
    public function saveInviteAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $objInviteFriend = new InviteFriend();
        $form = $this->createForm(new InviteFriendType(), $objInviteFriend);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {

            $form->bind($request);

            $validator = $this->container->get('validator');
            $errorList = $validator->validate($form);

            if (count($errorList) > 0) {
                $msg = "";
                foreach ($errorList as $err)
                    $msg .= $err->getMessage() . ";\n";

                $code = "ERR";
            } else {

                $em = $this->getDoctrine()->getManager();
                $data = $form->getData();
//                $objFriend = new InviteFriend();
//                $objFriend->setEmail($data['email']);
//                $objFriend->setUser($user);
                $objInviteFriend->setInvitationDate(new DateTime());
                $objInviteFriend->setIsActive(true);
                $em->persist($objInviteFriend);
                $em->flush();

                $message = \Swift_Message::newInstance()
                        ->setSubject('Invitation from \'' . $user->getFirstName() . ' ' . $user->getLastName() . '\' for DUSK')
                        ->setFrom('info@dusk.com')
                        ->setTo($objInviteFriend->getEmail())
                        ->setBody($this->renderView('DuskUserBundle:Email:inviteFriend.txt.twig', array('name' => $user->getFirstName() . ' ' . $user->getLastName())))
                        ->setContentType('text/html');
                $this->get('mailer')->send($message);

                $msg = "Your request has been sent to your friend!";
                $code = "OK";
            }

            $response = new Response(\json_encode(array('code' => $code, 'msg' => $msg)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
	
	/**
	 * Invite action for invite form view
	*/
    public function inviteAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $objInviteFriend = new InviteFriend();
        $form = $this->createForm(new InviteFriendType(), $objInviteFriend);

        return $this->render('DuskUserBundle:Default:inviteFriend.html.twig', array('form' => $form->createView()));
    }
	
	/**
	 * Newsletter action for newsletter form view
	*/
    public function newsletterAction() {

        $newsletter = new Newsletter();
        $form = $this->createForm(new NewsletterType(), $newsletter);

        return $this->render('DuskUserBundle:Default:newsletter.html.twig', array('form' => $form->createView()));
    }
	
	/**
	 * Save newsletter action for user subscribe
	 * @return json response
	*/
    public function saveNewsletterAction() {
        $newsletter = new Newsletter();
        $form = $this->createForm(new NewsletterType(), $newsletter);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {

            $form->bind($request);




//            $validator = $this->container->get('validator');
//            $errorList = $validator->validate($form);


            $emailConstraint = new Email();
            $data = $form->getData();
            // use the validator to validate the value
            $errorList = $this->get('validator')->validateValue(
                    $data->getEmail(), $emailConstraint
            );


            if (count($errorList) > 0) {
                $msg = "Enter valid email";
//                foreach ($errorList as $err)
//                    $msg .= $err->getMessage() . "\n";

                $code = "ERR";
            } else {
                $newsletter->setSignupDate(new DateTime());
                $newsletter->setIsActive(true);
                $em = $this->getDoctrine()->getManager();
                $em->persist($newsletter);
                $em->flush();

                $msg = "Thanks for subscription.";
                $code = "OK";
            }

            $response = new Response(\json_encode(array('code' => $code, 'msg' => $msg)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        return new Response("");
    }
	
	/**
	 * Load state action for get state name
	 * @return json response
	*/
    public function loadStateAction($id) {
        $em = $this->getDoctrine()->getManager();
        $arr = array();
        $records = $em->getRepository("DuskUserBundle:State")
                        ->createQueryBuilder('s')
                        ->where('s.country = :country')
                        ->setParameter(':country', $id)
                        ->orderBy('s.state_name', 'ASC')
                        ->getQuery()->getResult();
        foreach ($records as $record) {
            $arr[$record->getId()] = $record->getStateName();
        }
        return new JsonResponse($arr);
    }

}
