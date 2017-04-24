<?php

namespace Dusk\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dusk\UserBundle\Entity\VenueRepository;
use Dusk\UserBundle\Entity\RoomRepository;
use Dusk\UserBundle\Entity\mp3file;

class AlbumController extends Controller {
	
	/**
     * Index action for album list
    */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $albums = $em->getRepository('DuskUserBundle:Album')->findBy(array('is_active' => true));
        return $this->render('DuskUserBundle:Album:index.html.twig', array('albums' => $albums));
    }
	
	/**
     * Add track action for upload album
     * @id integer
     * @return json response
    */
    public function addTracksAction($id) {
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('DuskUserBundle:Album')->find($id);
        $trackArr = array();
        foreach ($album->getTracks() as $key => $track) {
            if(!$track->getIsPaid()) {
                    continue;
            }
            $trackArr[$key]['title'] = $track->getTitle();
            $trackArr[$key]['audio'] = $track->getAudio();
            $trackArr[$key]['artist'] = $track->getArtist();
        }

        shuffle($trackArr);

        return new JsonResponse(array('img' => $album->getImage(), 'data' => $trackArr));
    }
	
	/**
     * Free track action for album .
     * @id integer
     * @return json response
    */
    public function freeTracksAction($id) {
        $em = $this->getDoctrine()->getManager();

        $tracks = $em->getRepository('DuskUserBundle:Track')->createQueryBuilder('t')
                        ->select('t.title, t.audio, t.artist')
                        ->leftJoin('t.albums', 'a')
                        ->where('t.is_paid = 0')
                        ->andWhere('a.id = :album')
                        ->setParameter('album', $id)
                        ->getQuery()->getArrayResult();
        shuffle($tracks);
        return new JsonResponse($tracks);
    }
	
	/**
     * Add action for album and room.
     * @albumId integer
	 * @roomId integer
    */
    public function addAction($albumId, $roomId) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('DuskUserBundle:Room')->find($roomId);
        $album = $em->getRepository('DuskUserBundle:Album')->find($albumId);

        try {
            $room->addAlbum($album);
            $em->persist($room);
            $em->flush();
        } catch (\Exception $e) {
            echo 'Playlist already added to room.';
            exit;
        }

        echo 'Playlist added to room.';
        exit;

        //return $this->render('DuskUserBundle:Album:add.html.twig', array('venues' => $venues));
    }
	
	/**
     * Remove action for album and room.
     * @albumId integer
	 * @roomId integer
    */
    public function removeAction($albumId, $roomId) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('DuskUserBundle:Room')->find($roomId);
        $album = $em->getRepository('DuskUserBundle:Album')->find($albumId);

        $room->removeAlbum($album);
        $em->persist($room);
        $em->flush();

        echo 'Playlist removed from room.';
        exit;
    }
	
	/**
     * Add action for album list.
     * @id integer
    */
    public function updateAlbumListAction($id) {
        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('DuskUserBundle:Room')->find($id);
        $details['albumCount'] = '';

        $albumArr = array();
        $albumTime = array();
        foreach ($room->getAlbums() as $alb) {
            if ($alb->getIsActive()) {
                $albumArr[] = $alb;
                $details['albumCount'] += 1;
            }
            foreach ($alb->getTracks() as $key => $track) {
                if(!$track->getIsPaid()) {
                    continue;
                }
                $m = new mp3file("uploads/track/audio/" . $track->getAudio());
                $a = $m->get_metadata();

                if ($key == 0) {
                    $albumTime[$alb->getId()]['time'] = $a['Length mm:ss'];
                } else {
                    $albumTime[$alb->getId()]['time'] = $this->sum_the_time($albumTime[$alb->getId()]['time'], $a['Length mm:ss']);
                }
            }
        }

        return $this->render('DuskUserBundle:Album:updateRoom.html.twig', array(
                    'myalbums' => $albumArr,
                    'currentRoom' => $room,
                    'details' => $details,
                    'duration' => $albumTime
        ));
    }
	
	/**
     * Add action for album.
     * @albumId integer
     * @return form and album data
    */
    public function addAlbumAction($albumId) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

		//$venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId(), 'is_active' => true));
        $album = $em->getRepository('DuskUserBundle:Album')->find($albumId);

        $form = $this->createFormBuilder()
                ->add('venue', 'entity', array(
                    'class' => 'DuskUserBundle:Venue',
                    'empty_value' => 'Please Select',
                    'property' => 'name',
                    'query_builder' => function(VenueRepository $er) {
                return $er->createQueryBuilder('v')
                        ->where('v.admin = :admin')
                        ->setParameter('admin', $this->container->get('security.context')->getToken()->getUser()->getId())
                        ->andWhere('v.is_active = :active')
                        ->setParameter('active', 1)
                ;
            }, 'multiple' => false, 'expanded' => false, 'required' => true))
                ->add('room', 'entity', array(
                    'class' => 'DuskUserBundle:Room',
                    'empty_value' => 'Please Select',
                    'property' => 'name',
                    'query_builder' => function(RoomRepository $er) {
                return $er->createQueryBuilder('r')
                        ->add('orderBy', 'r.name ASC')
                        ->where('1=0');
            },
                    'required' => true
                ))
                ->add('album', 'hidden', array('data' => $albumId))
                ->getForm();

        return $this->render('DuskUserBundle:Album:addAlbum.html.twig', array(
                    'form' => $form->createView(),
                    'album' => $album,
        ));
    }
	
	/**
     * Save action for album.
     * @albumId integer
     * @return json response
    */
    public function saveAlbumAction($albumId) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->get('request');


        $form = $this->createFormBuilder()
                ->add('venue', 'entity', array(
                    'class' => 'DuskUserBundle:Venue',
                    'empty_value' => 'Please Select',
                    'property' => 'name',
                    'query_builder' => function(VenueRepository $er) {
                return $er->createQueryBuilder('v')
                        ->where('v.admin = :admin')
                        ->setParameter('admin', $this->container->get('security.context')->getToken()->getUser()->getId());
            }, 'multiple' => false, 'expanded' => false, 'required' => false))
                ->add('room', 'entity', array(
                    'class' => 'DuskUserBundle:Room',
                    'empty_value' => 'Please Select',
                    'property' => 'name',
                    'query_builder' => function(RoomRepository $er) {
                return $er->createQueryBuilder('r')
                        ->add('orderBy', 'r.name ASC');
            },
                    'required' => false
                ))
                ->add('album', 'hidden', array('data' => $albumId))
                ->getForm();

        if ($request->getMethod() == 'POST') {

            $form->bind($request);
            $data = $form->getData();


            $validator = $this->container->get('validator');
            $errorList = $validator->validate($form);

            if (count($errorList) > 0) {
                $msg = "";
                foreach ($errorList as $err)
                    $msg .= $err->getMessage() . ";\n";

                $code = "ERR";
            } else {

                $data = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $room = $em->getRepository('DuskUserBundle:Room')->find($data['room']->getId());
                $album = $em->getRepository('DuskUserBundle:Album')->find($data['album']);

                try {
                    $room->addAlbum($album);
                    $em->persist($room);
                    $em->flush();
                    $msg = 'Playlist added to room.';
                } catch (\Exception $e) {
                    $msg = 'Playlist already added to room.';
                }


                $code = "OK";
            }

            $response = new Response(\json_encode(array('code' => $code, 'msg' => $msg)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        return new Response("");
    }
	
	/**
     * Load room action for room list.
     * @venueId integer
     * @return json response
    */
    public function loadRoomAction($venueId) {
        $em = $this->getDoctrine()->getManager();
        $venue = $em->getRepository('DuskUserBundle:Venue')->find($venueId);
        foreach ($venue->getRooms() as $record) {
            if ($record->isActiveRoom()) {
                $arr[$record->getId()] = $record->getName();
            }
        }
        return new JsonResponse($arr);
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

}
