<?php

namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Pages
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email is already in use")
 * @ORM\Table(name="newsletter")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\NewsletterRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Newsletter {

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
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $email;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="signup_date", type="datetime")
     * 
     */
    private $signup_date;

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
     * @return Newsletter
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
     * Set email
     *
     * @param string $email
     * @return Newsletter
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set signup_date
     *
     * @param \DateTime $signupDate
     * @return Newsletter
     */
    public function setSignupDate($signupDate)
    {
        $this->signup_date = $signupDate;
    
        return $this;
    }

    /**
     * Get signup_date
     *
     * @return \DateTime 
     */
    public function getSignupDate()
    {
        return $this->signup_date;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return Newsletter
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
}