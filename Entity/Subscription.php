<?php

namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscription")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\SubscriptionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Subscription {

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
     * @ORM\Column(type="float", nullable=false)
     */
    protected $price;
    
    /**
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $month;

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
    public function getId() {
        return $this->id;
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Genre
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
     * @return Album
     */
    public function setIsActive($isActive) {
        $this->is_active = $isActive;

        return $this;
    }

    /**
     * Get is_active
     *
     * @return boolean 
     */
    public function getIsActive() {
        return $this->is_active;
    }
    
    public function __toString() {
        return $this->name ? $this->name : '';
    }


    /**
     * Set price
     *
     * @param float $price
     * @return Subscription
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set month
     *
     * @param integer $month
     * @return Subscription
     */
    public function setMonth($month)
    {
        $this->month = $month;
    
        return $this;
    }

    /**
     * Get month
     *
     * @return integer 
     */
    public function getMonth()
    {
        return $this->month;
    }
}