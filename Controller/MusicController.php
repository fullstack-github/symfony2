<?php

namespace Dusk\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Dusk\UserBundle\Entity\Playlist;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dusk\UserBundle\Entity\mp3file;

class MusicController extends Controller {
	
	/**
	 * Index action for get dusk user
	*/
    public function indexAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user->hasRole('ROLE_SUPER_ADMIN')) {
            return $this->redirect($this->generateUrl('fos_user_security_logout'));
        }
        
        $em = $this->getDoctrine()->getManager();

        $details['activeVenueCount'] = 0;

        if ($user->hasRole('ROLE_ADMIN')) {
            $venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId()));

            foreach ($venues as $key => $venue) {
                if ($venue->getIsActive())
                    $details['activeVenueCount'] += 1;
            }

            $details['currentVenue'] = '';
            $details['currentRoom'] = '';
            $details['venueCount'] = count($venues);

            if ($details['activeVenueCount'] == 1) {
                foreach ($venues as $venue) {
                    if ($venue->getIsActive()) {
                        return $this->redirect($this->generateUrl('dusk_change_venue', array('id' => $venue->getId())));
                    }
                }
            }
        } else {
            $room = $em->getRepository('DuskUserBundle:Room')->findOneBy(array('user' => $user->getId()));

            if ($room) {

                $venues = $room->getVenue();

                if ($venues) {
                    $details['activeVenueCount'] = 1;
                    $details['currentVenue'] = '';
                    $details['currentRoom'] = '';
                    $details['venueCount'] = 1;
                    
                    foreach ($venues->getRooms() as $room) {
                        if ($room->isActiveRoom() and $room->getUser()->getId() == $user->getId()) {
                            return $this->redirect($this->generateUrl('dusk_change_room', array('id' => $room->getId())));
                        }
                    }
                    
                    return $this->redirect($this->generateUrl('dusk_change_venue', array('id' => $venues->getId())));
                }
            }
        }
        

        return $this->render('DuskUserBundle:Music:index.html.twig', array(
                    'venues' => $venues,
                    'details' => $details,
        ));
    }
	
	/**
	 * Venue action for music venue as given argument
	 * @id integer
	 * return venue details
	*/
    public function VenueAction($id) {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $venue = $em->getRepository('DuskUserBundle:Venue')->find($id);

        $details['currentVenue'] = $venue;
        $details['currentRoom'] = '';
        $details['activeRoomCount'] = '';

        foreach ($venue->getRooms() as $key => $room) {
            if ($room->isActiveRoom()) {
                $details['activeRoomCount'] += 1;
            }
        }

        if ($details['activeRoomCount'] == 1) {
            foreach ($venue->getRooms() as $room) {
                if ($room->isActiveRoom()) {
                    return $this->redirect($this->generateUrl('dusk_change_room', array('id' => $room->getId())));
                }
            }
            
        }

        return $this->render('DuskUserBundle:Music:venue.html.twig', array(
                    'venue' => $venue,
                    'details' => $details,
        ));
    }
	
	/**
	 * Room action for getting room details
	 * @id integer
	 * @return venue, album and room details 
	*/
    public function RoomAction($id) {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $room = $em->getRepository('DuskUserBundle:Room')->createQueryBuilder('r')
                        ->where('r.admin = :admin OR r.user = :user')
                        ->setParameter('admin', $user->getId())
                        ->setParameter('user', $user->getId())
                        ->andWhere('r.id = :room')
                        ->setParameter('room', $id)
                        ->getQuery()->getOneOrNullResult();
        $isActiveRoom = $room->isActiveRoom();
        if (!$room) {
            $this->get('session')->getFlashBag()->add('notice', 'Room not found.');
            return $this->redirect($this->generateUrl('dusk_mymusic'));
        }

        if (!$room->getIsActive()) {
            $this->get('session')->getFlashBag()->add('notice', 'Room is not active.');
            return $this->redirect($this->generateUrl('dusk_mymusic'));
        }

        $session = $this->getRequest()->getSession();

        // store an attribute for reuse during a later user request
        $session->set('roomDetail', $room->getName());

        // in another controller for another request
        $foo = $session->get('roomDetail');
        $venue = $room->getVenue();
        $details['currentVenue'] = $room->getVenue();
        $details['currentRoom'] = $room;
        $details['activeRoomCount'] = '';
        $details['albumCount'] = '';
        $albumArr = array();
        $albumTime = array();
        foreach ($room->getAlbums() as $alb) {
            if ($alb->getIsActive()) {
                $albumArr[] = $alb;
                $details['albumCount'] += 1;

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
        }
        foreach ($venue->getRooms() as $key => $room) {
            if ($room->isActiveRoom()) {
                $details['activeRoomCount'] += 1;
            }
        }
        
        $albums = $em->getRepository('DuskUserBundle:Album')->findBy(array('is_active' => true));
        $albums = $em->getRepository('DuskUserBundle:Album')->createQueryBuilder('a')
                        ->leftJoin('a.tracks', 't')
                        ->where('t.is_active = 1')
                        ->andWhere('a.is_active = 1')
                        ->andWhere('t.is_paid = 1')
                        ->getQuery()
                        ->getResult();
        
        $albumTime = array();
        foreach ($albums as $album) {

            foreach ($album->getTracks() as $key => $track) {
                if(!$track->getIsPaid()) {
                    continue;
                }
                $m = new mp3file("uploads/track/audio/" . $track->getAudio());
                $a = $m->get_metadata();
                
                if ($key == 0) {
                    $albumTime[$album->getId()]['time'] = $a['Length mm:ss'];
                } else {
                   $albumTime[$album->getId()]['time'] = $this->sum_the_time($albumTime[$album->getId()]['time'], $a['Length mm:ss']);
                }
//                echo $track->getTitle(). ' - '. $a['Length mm:ss']."<br />";
            }
        }
        return $this->render('DuskUserBundle:Music:room.html.twig', array(
                    'venue' => $venue,
                    'details' => $details,
                    'albums' => $albums,
                    'myalbums' => $albumArr,
                    'currentRoom' => $details['currentRoom'],
                    'duration' => $albumTime,
                    'isRoomActive' => $isActiveRoom,
        ));
    }
	
	/**
	 * play music action for music details
	 * @id integer
	 * @return music details
	*/
    public function playMusicAction($id) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId()));

        $activeVenue = 0;
        $inactiveVenue = 0;
        foreach ($venues as $venue) {
            if ($venue->getIsActive())
                $activeVenue += 1;
        }

        $venue = $em->getRepository('DuskUserBundle:Venue')->find($id);

        $tracks = $em->getRepository("DuskUserBundle:Track")->createQueryBuilder('t')
                        ->getQuery()->getArrayResult();
        $albums = $em->getRepository("DuskUserBundle:Album")->createQueryBuilder('t')
                        ->getQuery()->getResult();

        return $this->render('DuskUserBundle:Music:playMusic.html.twig', array(
                    'tracks' => $tracks,
                    'albums' => $albums,
                    'activeVenue' => $activeVenue,
                    'venueCount' => count($venues),
                    'trackCount' => count($tracks),
                    'venues' => $venues,
                    'venue' => $venue,
                    'albumCount' => count($albums),
                    'roomCount' => count($venue->getRooms())
        ));
    }
	
	/**
	 * Get track action for get track details
	 * @id integer albumID
	 * @return title and audio details
	*/
    public function getTracksAction($id) {
        $em = $this->getDoctrine()->getManager();

        $album = $em->getRepository('DuskUserBundle:Album')->createQueryBuilder('a')
                        ->leftJoin('a.tracks', 't')
                        ->where('a.id = :album AND t.is_paid = 0')->setParameter('album', $id)->getQuery()->getResult();

        $tracks = array();

        foreach ($album->getTracks() as $key => $track) {
            $tracks[$key]['title'] = $track->getTitle();
            $tracks[$key]['audio'] = $track->getAudio();
        }

        return new JsonResponse($tracks);
    }
	
	/**
	 * Change room action for change room
	 * @id integer
	*/
    public function changeRoomAction($id) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $venues = $em->getRepository('DuskUserBundle:Venue')->findBy(array('admin' => $user->getId()));

        $room = $em->getRepository('DuskUserBundle:Room')->find($id);

        $activeVenue = 0;
        $inactiveVenue = 0;
        foreach ($venues as $venue) {
            if ($venue->getIsActive())
                $activeVenue += 1;
        }

        $tracks = $em->getRepository("DuskUserBundle:Track")->createQueryBuilder('t')
                        ->getQuery()->getArrayResult();
        $albums = $em->getRepository("DuskUserBundle:Album")->createQueryBuilder('t')
                        ->getQuery()->getResult();

        return $this->render('DuskUserBundle:Music:playMusic.html.twig', array(
                    'tracks' => $tracks,
                    'albums' => $albums,
                    'activeVenue' => $activeVenue,
                    'venueCount' => count($venues),
                    'trackCount' => count($tracks),
                    'venues' => $venues,
                    'venue' => $room->getVenue(),
//                    'albumCount' => count($albums),
                    'currentRoom' => $room,
                    'roomCount' => count($room->getVenue()->getRooms())
        ));
    }
	
	/**
	 * New play list action for get new play list
	*/
    public function newPlaylistAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('DuskUserBundle:Room')->findOneBy(array('user' => $user->getId()));
        $venue = $em->getRepository('DuskUserBundle:Venue')->find($room->getVenue()->getId());

        $form = $this->createFormBuilder()
                ->add('venue', 'hidden', array('data' => $venue->getId()))
                ->add('room', 'hidden', array('data' => $room->getId()))
                ->add('user', 'hidden', array('data' => $user->getId()))
                ->add('name', 'text', array('required' => true))
                ->add('is_active', 'checkbox', array('required' => false))
                ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {

            $form->bind($request);

            if ($form->isValid()) {
                $playlistData = $form->getData();
                $playlist = new Playlist();
                $playlist->setUser($user);
                $playlist->setRoom($room);
                $playlist->setVenue($venue);
                $playlist->setName($playlistData['name']);
                $playlist->setIsActive($playlistData['is_active']);

                $em->persist($playlist);
                $em->flush();
                $this->get('session')->getFlashBag()->add('notice', 'New playlist is created successfully.');
                return $this->redirect($this->generateUrl('dusk_mymusic'));
            }
        }

        return $this->render('DuskUserBundle:Music:newPlaylist.html.twig', array('room' => $room, 'form' => $form->createView()));
    }

    public function playingNowAction($id) {
        
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
     * Play room track action for get title, audio and artist.
     * @id integer
     * @return json response
    */
    public function playRoomTracksAction($id) {
        $em = $this->getDoctrine()->getManager();
        $room = $em->getRepository('DuskUserBundle:Room')->find($id);

        $trackArr = array();
        $count = 0;
        foreach ($room->getAlbums() as $album) {
            foreach ($album->getTracks() as $track) {
                $trackArr[$count]['title'] = $track->getTitle();
                $trackArr[$count]['audio'] = $track->getAudio();
                $trackArr[$count]['artist'] = $track->getArtist();
                $count++;
            }
        }

        shuffle($trackArr);

        return new JsonResponse($trackArr);
    }

}
