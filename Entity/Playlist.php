<?php

namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="playlist")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\PlaylistRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Playlist {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="playlists")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    protected $room;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="playlists")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Venue", inversedBy="playlists")
     * @ORM\JoinColumn(name="venue_id", referencedColumnName="id")
     */
    protected $venue;

    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    protected $name;
    
    /**
     * @ORM\Column(type="boolean")
     *
     */
    protected $is_active;
    
    
    public function __construct() {
        $this->is_active = true;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set name
     *
     * @param string $name
     * @return Playlist
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return Playlist
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;
    
        return $this;
    }

    /**
     * Get is_active
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Set room
     *
     * @param \Dusk\UserBundle\Entity\Room $room
     * @return Playlist
     */
    public function setRoom(\Dusk\UserBundle\Entity\Room $room = null)
    {
        $this->room = $room;
    
        return $this;
    }

    /**
     * Get room
     *
     * @return \Dusk\UserBundle\Entity\Room 
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set user
     *
     * @param \Dusk\UserBundle\Entity\User $user
     * @return Playlist
     */
    public function setUser(\Dusk\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Dusk\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set venue
     *
     * @param \Dusk\UserBundle\Entity\Venue $venue
     * @return Playlist
     */
    public function setVenue(\Dusk\UserBundle\Entity\Venue $venue = null)
    {
        $this->venue = $venue;
    
        return $this;
    }

    /**
     * Get venue
     *
     * @return \Dusk\UserBundle\Entity\Venue 
     */
    public function getVenue()
    {
        return $this->venue;
    }
}