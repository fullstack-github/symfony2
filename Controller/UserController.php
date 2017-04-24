<?php

namespace Dusk\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller {
	
	/**
	 * Resend credential action for user 
	 * @return dusk account url
	*/
    public function resendCredentialAction($id) {
        $em = $this->getDoctrine()->getManager();
        $objRoom = $em->getRepository('DuskUserBundle:Room')->find($id);

        $objUser = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($objRoom->getUser()->getUsername());
        $userManager = $this->get('fos_user.user_manager');
        $new_password = str_replace(' ', '', $objRoom->getName()) . $objRoom->getVenue()->getId() . $objRoom->getId();
        $objUser->setPlainPassword($new_password);
        $userManager->updateUser($objUser);
        $msg = "Hello,<br /><br />Your Login Email: " . $objRoom->getUser()->getUsername() . "<br />Password: " . $new_password . "<br /><br />Thank You,<br />Dusk Team";
        $message = \Swift_Message::newInstance()
                ->setSubject('Your Login Credentials for Dusk')
                ->setFrom('info@dusk.com')
                ->setTo($objRoom->getUser()->getUsername())
                ->setBody($msg)
                ->setContentType("text/html");
        $this->get('mailer')->send($message);

        return $this->redirect($this->generateUrl('dusk_account'));
    }

}
