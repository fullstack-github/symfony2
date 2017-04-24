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
 * @ORM\Table(name="invite_friend")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\InviteFriendRepository")
 * @ORM\HasLifecycleCallbacks
 */
class InviteFriend {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $email;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friends")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var \DateTime
     * @ORM\Column(name="invitation_date", type="datetime")
     * 
     */
    private $invitation_date;

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
     * Set email
     *
     * @param string $email
     * @return InviteFriend
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
     * Set invitation_date
     *
     * @param \DateTime $invitationDate
     * @return InviteFriend
     */
    public function setInvitationDate($invitationDate)
    {
        $this->invitation_date = $invitationDate;
    
        return $this;
    }

    /**
     * Get invitation_date
     *
     * @return \DateTime 
     */
    public function getInvitationDate()
    {
        return $this->invitation_date;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return InviteFriend
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
     * Set user
     *
     * @param \Dusk\UserBundle\Entity\User $user
     * @return InviteFriend
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
}