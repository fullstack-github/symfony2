<?php

namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="venue")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\VenueRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Venue {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     */
    protected $slug;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="venues")
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="id")
     */
    protected $admin;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    protected $is_active;
    
    /**
     * @ORM\OneToMany(targetEntity="Room", mappedBy="venue")
     */
    protected $rooms;

    public function __construct() {
        $this->is_active = true;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
    

    /**
     * Set name
     *
     * @param string $name
     * @return Venue
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
     * @return Venue
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
     * Set admin
     *
     * @param \Dusk\UserBundle\Entity\User $admin
     * @return Venue
     */
    public function setAdmin(\Dusk\UserBundle\Entity\User $admin = null)
    {
        $this->admin = $admin;
    
        return $this;
    }

    /**
     * Get admin
     *
     * @return \Dusk\UserBundle\Entity\User 
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    


    /**
     * Add rooms
     *
     * @param \Dusk\UserBundle\Entity\Room $rooms
     * @return Venue
     */
    public function addRoom(\Dusk\UserBundle\Entity\Room $rooms)
    {
        $this->rooms[] = $rooms;
    
        return $this;
    }

    /**
     * Remove rooms
     *
     * @param \Dusk\UserBundle\Entity\Room $rooms
     */
    public function removeRoom(\Dusk\UserBundle\Entity\Room $rooms)
    {
        $this->rooms->removeElement($rooms);
    }

    /**
     * Get rooms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Venue
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
}