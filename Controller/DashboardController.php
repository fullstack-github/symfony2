<?php

namespace Dusk\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Dusk\UserBundle\Form\Type\UserFormType;
use Doctrine\ORM\EntityRepository;
use Dusk\UserBundle\Entity\Album;
use Dusk\UserBundle\Entity\mp3file;
use \DateTime;

class DashboardController extends Controller {
	
	/**
	 * Index action for get template of venue
	*/
    public function indexAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId()));

        $activeVenue = 0;
        $inactiveVenue = 0;
        if ($venues) {
            foreach ($venues as $venue) {
                if ($venue->getIsActive())
                    $activeVenue += 1;
            }
        }


        $form = $this->createForm(new UserFormType(), $user);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {

            $form->bind($request);

            if ($form->isValid()) {
                $data = $form->getData();
//                $username = $user->getUsername();
//                $email = $user->getEmail();
                $em->persist($user);
                
                $userManager = $this->get('fos_user.user_manager');
                $user->setPlainPassword($data->getPlainPassword());
                $userManager->updateUser($user);
                
//                $user->setUsername($username);
//                $user->setEmail($email);
//                $em->persist($user);
                
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'Your profile has been updated!');
                return $this->redirect($this->generateUrl('dusk_dashboard'));
            }
        }

        return $this->container->get('templating')->renderResponse('DuskUserBundle:Dashboard:index.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user,
                    'activeVenue' => $activeVenue,
                    'venueCount' => count($venues),
        ));
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
            $partners[$key]['image'] = "uploads/partner/" . $record['image'];
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

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($enquiry);
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'Your request has been submitted!');
                return $this->redirect($this->generateUrl('dusk_contactus'));
            }
        }
        return $this->render('DuskUserBundle:Default:contact.html.twig', array('form' => $form->createView()));
    }

	/**
	 * Overview action for venue overview
	*/
    public function overviewAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId()));

        return $this->render('DuskUserBundle:Dashboard:overview.html.twig', array('venues' => $venues, 'venueCount' => count($venues)));
    }
	
	/**
	 * Account action for venue
	 * @return venue details
	*/
    public function accountAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId()));
		//$venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => 1));
        $arr = array();
        $forms = array();

        foreach ($venues as $venue) {
            $arr[$venue->getId()]['id'] = $venue->getId();
            $arr[$venue->getId()]['name'] = $venue->getName();
            $arr[$venue->getId()]['status'] = $venue->getIsActive();
            $arr[$venue->getId()]['rooms'] = count($venue->getRooms());
            $arr[$venue->getId()]['is_active'] = $venue->getIsActive();

            foreach ($venue->getRooms() as $room) {
                $arr[$venue->getId()]['room_details'][$room->getId()]['id'] = $room->getId();
                $arr[$venue->getId()]['room_details'][$room->getId()]['name'] = $room->getName();
                $arr[$venue->getId()]['room_details'][$room->getId()]['subscription'] = $room->getSubscription()->getId();
                $arr[$venue->getId()]['room_details'][$room->getId()]['expires_at'] = $room->getExpiredAt();
                $arr[$venue->getId()]['room_details'][$room->getId()]['is_active'] = $room->getIsActive();
                $arr[$venue->getId()]['room_details'][$room->getId()]['user_id'] = $room->getUser()->getId();
                $arr[$venue->getId()]['room_details'][$room->getId()]['username'] = $room->getUser()->getEmail();
                $datetime = new DateTime('now');

                $form = $this->createFormBuilder()
                        ->add('venue', 'hidden', array('data' => $venue->getId()))
                        ->add('name', 'text', array('data' => $room->getName()))
                        ->add('subscription', 'entity', array('disabled' => $room->getExpiredAt()->format('Y-m-d') > $datetime->format('Y-m-d') ? true : false,
                            'class' => 'DuskUserBundle:Subscription',
                            'property' => 'name',
                            'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                                ->add('orderBy', 'e.price ASC');
                    }, 'label' => 'Your Current Subscription', 'multiple' => false, 'expanded' => true, 'required' => true, 'data' => $room->getSubscription()
                        ))
                        ->add('username', 'text', array('required' => true, 'data' => $room->getUser()->getUsername()))
                        ->add('password', 'password', array('required' => true))
                        ->getForm();

                $forms[$venue->getId()][$room->getId()]['form'] = $form->createView();
            }
        }

        return $this->render('DuskUserBundle:Dashboard:account.html.twig', array(
                    'venues' => $venues,
                    'venueCount' => count($venues),
                    'venueDetails' => $arr,
                    'forms' => $forms
        ));
    }
	
	/**
	 * Instant preview action for album
	 * @return album array
	*/
    public function instantPreviewAction() {
        $em = $this->getDoctrine()->getManager();
        $albums = $em->getRepository('DuskUserBundle:Album')->createQueryBuilder('a')
                        ->leftJoin('a.tracks', 't')
                        ->where('t.is_paid = 0')->getQuery()->getResult();
        
        $albumTime = array();
        foreach ($albums as $album) {

            foreach ($album->getTracks() as $key => $track) {
                if($track->getIsPaid()) continue;
                
                $m = new mp3file("uploads/track/audio/" . $track->getAudio());
                $a = $m->get_metadata();
               //echo "<pre>";
//print_r($a);
//exit; 
                if ($key == 0) {
                    $albumTime[$album->getId()]['time'] = $a['Length mm:ss'];
                } else {
                  if(isset($a['Length mm:ss']) && $a['Length mm:ss'] != "")
                      $albumTime[$album->getId()]['time'] = $this->sum_the_time($albumTime[$album->getId()]['time'], $a['Length mm:ss']);
                }
//                echo $track->getTitle(). ' - '. $a['Length mm:ss']."<br />";
            }
        }        
        return $this->render('DuskUserBundle:Dashboard:instantPreview.html.twig', array(
                    'albums' => $albums,
                    'duration' => $albumTime
        ));
    }
	
	/**
     * Sum of two time.
     * @time1 DATETIME
	 * @time2 DATETIME
     * @return date different
    */
    public function sum_the_time($time1, $time2) {
        $times = array($time1, $time2);
        $seconds = 0;
        foreach ($times as $time) {
            list($hour, $minute, $second) = explode(':', $time);
            $seconds += $hour * 3600;
            $seconds += $minute * 60;
            $seconds += $second;
        }
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        // return "{$hours}:{$minutes}:{$seconds}";
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds); // Thanks to Patrick
    }
	
	/**
     * rightInstant preview  for track duration.
     * @return albums and duration
    */
    public function rightInstantPreviewAction() {
        $em = $this->getDoctrine()->getManager();
        $albums = $em->getRepository('DuskUserBundle:Album')->createQueryBuilder('a')
                        ->leftJoin('a.tracks', 't')
                        ->where('t.is_paid = 0')->getQuery()->getResult();

        $keys = array_rand($albums, 2);

        $selectedAlbums = array();
        $selectedAlbums[0] = $albums[$keys[0]];
        $selectedAlbums[1] = $albums[$keys[1]];
        
        $albumTime = array();
        foreach ($selectedAlbums as $album) {

            foreach ($album->getTracks() as $key => $track) {
                if($track->getIsPaid()) continue;
                
                $m = new mp3file("uploads/track/audio/" . $track->getAudio());
                $a = $m->get_metadata();

                if ($key == 0) {
                    $albumTime[$album->getId()]['time'] = $a['Length mm:ss'];
                } else {
                    $albumTime[$album->getId()]['time'] = $this->sum_the_time($albumTime[$album->getId()]['time'], $a['Length mm:ss']);
                }
            }
        }

        return $this->render('DuskUserBundle:Dashboard:rightInstantPreview.html.twig', array(
                    'albums' => $selectedAlbums,
                    'duration' => $albumTime
        ));
    }
	
	/**
     * CMS action for records
     * @return cms records
    */
    public function cmsAction($slug) {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('DuskUserBundle:CMSPage')->findOneByRoute($slug);
        return $this->render('DuskUserBundle:Dashboard:cmspage.html.twig', array(
                    'record' => $record
        ));
    }
}