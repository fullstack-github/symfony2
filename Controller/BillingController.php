<?php

namespace Dusk\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BillingController extends Controller {
	
	/**
	 * index the input given as first argument.
	 * @return room and user records
	*/
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId()));
                
        $records = $em->getRepository('DuskUserBundle:Room')->createQueryBuilder('r')
                ->where('r.admin = :admin')
                ->setParameter('admin', $user->getId())
                ->andWhere('r.payment_status = :payment')
                ->setParameter('payment', 1)
                ->getQuery()->getResult();
        return $this->render('DuskUserBundle:Billing:index.html.twig', array('records' => $records, 'venueCount' => count($venues), 'user' => $user));
    }

}
