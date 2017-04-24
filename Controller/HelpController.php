<?php

namespace Dusk\UserBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HelpController extends Controller {
	
	/**
	 * Index action for get help records
	*/
    public function indexAction($type) {
        $em = $this->getDoctrine()->getManager();
        $records = $em->getRepository("DuskUserBundle:HelpPage")->findBy(array('type' => $type));
        return $this->render('DuskUserBundle:Help:index.html.twig', array('records' => $records));
    }
    
	/**
	 * View action for contact form
	 * @slug string
	*/
    public function viewAction($slug) {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository("DuskUserBundle:HelpPage")->findOneBy(array('slug' => str_replace('-', ' ', $slug)));
        return $this->render('DuskUserBundle:Help:view.html.twig', array('record' => $record));
    }
}
