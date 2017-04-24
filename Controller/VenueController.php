<?php

namespace Dusk\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dusk\UserBundle\Entity\Venue;

class VenueController extends Controller {
	
	/**
	 * Index action for venue rooms
	*/
    public function indexAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $venues = array();
        $venueRooms = array();
        
        $venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId()));

        foreach ($venues as $venue) {
            foreach ($venue->getRooms() as $room) {
                $venueRooms[$venue->getName()] = isset($venueRooms[$venue->getName()]) ? $venueRooms[$venue->getName()] + 1 : 1;
            }
        }

        $activeVenue = 0;
        $inactiveVenue = 0;
        foreach ($venues as $venue) {
            if ($venue->getIsActive())
                $activeVenue += 1;
            else
                $inactiveVenue += 1;
        }

        return $this->render('DuskUserBundle:Venue:index.html.twig', array(
                    'venues' => $venues,
                    'active' => $activeVenue,
                    'inactive' => $inactiveVenue,
                    'venueRooms' => $venueRooms
        ));
    }

    public function newAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $venueRooms = array();
        
        $venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId()));
        
        if($user->getIsFreePeriod() and count($venues) >= 1) {
            $this->get('session')->getFlashBag()->add('notice', 'You are allowed to add one venue only.');
            return $this->redirect($this->generateUrl('dusk_venue'));
        }

        foreach ($venues as $venue) {
            foreach ($venue->getRooms() as $room) {
                $venueRooms[$venue->getName()] = isset($venueRooms[$venue->getName()]) ? $venueRooms[$venue->getName()] + 1 : 1;
            }
        }

        $activeVenue = 0;
        $inactiveVenue = 0;
        foreach ($venues as $venue) {
            if ($venue->getIsActive())
                $activeVenue += 1;
            else
                $inactiveVenue += 1;
        }
        $objVenue = new Venue();
        $form = $this->createFormBuilder($objVenue)
                ->add('name', 'text')
                ->add('is_active', null, array('required' => false))
                ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {

            $form->bind($request);

            if ($form->isValid()) {
                $data = $form->getData();
                
                $objVenue->setAdmin($user);
                $objVenue->setSlug(strtolower(str_replace(' ', '-',$objVenue->getName())));

                $em = $this->getDoctrine()->getManager();
                $em->persist($objVenue);
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'New Venue added succesfully!');
                return $this->redirect($this->generateUrl('dusk_venue'));
            }
        }
        return $this->render('DuskUserBundle:Venue:new.html.twig', array(
                    'form' => $form->createView(),
                    'venue' => $objVenue,
                    'active' => $activeVenue,
                    'inactive' => $inactiveVenue,
        ));
    }
	
	/**
	 * Detail action for venue and rooms 
	 * @return venue list and room details
	*/
    public function detailAction($id) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $venueList = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId()));

        $objVenue = $em->getRepository('DuskUserBundle:Venue')->find($id);

        $rooms = $em->getRepository('DuskUserBundle:Room')->findBy(array('venue' => $id));

        $activeRoom = 0;
        $inactiveRoom = 0;
        
        foreach ($rooms as $room) {
            if ($room->isActiveRoom())
                $activeRoom += 1;
            else
                $inactiveRoom += 1;
        }
        return $this->render('DuskUserBundle:Venue:detail.html.twig', array(
                    'venue' => $objVenue,
                    'active' => $activeRoom,
                    'inactive' => $inactiveRoom,
                    'venueList' => $venueList,
                    'venueCount' => count($venueList),
                    'rooms' => $rooms,
                    'roomCount' => count($rooms),
        ));
    }
	
	/**
	 * Change activation action for user account 
	 * @return dusk account url
	*/
    public function changeActivationAction($id) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $objVenue = $em->getRepository('DuskUserBundle:Venue')->find($id);
        if ($objVenue->getAdmin()->getId() == $user->getId()) {
            $objVenue->setIsActive($objVenue->getIsActive() ? false : true);
            $em->persist($objVenue);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('dusk_account'));
    }

}
