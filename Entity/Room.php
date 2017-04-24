<?php

namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dusk\UserBundle\Entity\Order;
use Dusk\UserBundle\Entity\Album;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;
/**
 * @ORM\Entity
 * @ORM\Table(name="room")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\RoomRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Room {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="rooms")
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="id")
     */
    protected $admin;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="room")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Venue", inversedBy="rooms")
     * @ORM\JoinColumn(name="venue_id", referencedColumnName="id")
     */
    protected $venue;

    /**
     * @ORM\ManyToOne(targetEntity="Subscription", inversedBy="room")
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id")
     */
    protected $subscription;

    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    protected $payment_status;

    /**
     * @ORM\column(type="datetime", nullable=false)
     * 
     */
    protected $started_at;

    /**
     * @ORM\column(type="datetime", nullable=false)
     * 
     */
    protected $expired_at;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    protected $is_active;

    /**
     * @ORM\ManyToMany(targetEntity="Album", inversedBy="rooms")
     * @ORM\JoinTable(name="rooms_albums",
     * joinColumns={@ORM\JoinColumn(name="room_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="album_id", referencedColumnName="id")}
     * )
     */
    protected $albums;

    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="room")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    protected $order;

    public function __construct() {
        $this->payment_status = false;
        $this->is_active = true;
        $this->started_at = new \DateTime();
        $this->expired_at = new \DateTime();
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
     * @return Room
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set payment_status
     *
     * @param boolean $paymentStatus
     * @return Room
     */
    public function setPaymentStatus($paymentStatus) {
        $this->payment_status = $paymentStatus;

        return $this;
    }

    /**
     * Get payment_status
     *
     * @return boolean 
     */
    public function getPaymentStatus() {
        return $this->payment_status;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return Room
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

    /**
     * Set admin
     *
     * @param \Dusk\UserBundle\Entity\User $admin
     * @return Room
     */
    public function setAdmin(\Dusk\UserBundle\Entity\User $admin = null) {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return \Dusk\UserBundle\Entity\User 
     */
    public function getAdmin() {
        return $this->admin;
    }

    /**
     * Set venue
     *
     * @param \Dusk\UserBundle\Entity\Venue $venue
     * @return Room
     */
    public function setVenue(\Dusk\UserBundle\Entity\Venue $venue = null) {
        $this->venue = $venue;

        return $this;
    }

    /**
     * Get venue
     *
     * @return \Dusk\UserBundle\Entity\Venue 
     */
    public function getVenue() {
        return $this->venue;
    }

    /**
     * Set subscription
     *
     * @param \Dusk\UserBundle\Entity\Subscription $subscription
     * @return Room
     */
    public function setSubscription(\Dusk\UserBundle\Entity\Subscription $subscription = null) {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * Get subscription
     *
     * @return \Dusk\UserBundle\Entity\Subscription 
     */
    public function getSubscription() {
        return $this->subscription;
    }

    /**
     * Set started_at
     *
     * @param \DateTime $startedAt
     * @return Room
     */
    public function setStartedAt($startedAt) {
        $this->started_at = $startedAt;

        return $this;
    }

    /**
     * Get started_at
     *
     * @return \DateTime 
     */
    public function getStartedAt() {
        return $this->started_at;
    }

    /**
     * Set expired_at
     *
     * @param \DateTime $expiredAt
     * @return Room
     */
    public function setExpiredAt($expiredAt) {
        $this->expired_at = $expiredAt;

        return $this;
    }

    /**
     * Get expired_at
     *
     * @return \DateTime 
     */
    public function getExpiredAt() {
        return $this->expired_at;
    }

    /**
     * Set user
     *
     * @param \Dusk\UserBundle\Entity\User $user
     * @return Room
     */
    public function setUser(\Dusk\UserBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Dusk\UserBundle\Entity\User 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set order
     *
     * @param \Dusk\UserBundle\Entity\Order $order
     * @return Room
     */
    public function setOrder(\Dusk\UserBundle\Entity\Order $order = null) {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \Dusk\UserBundle\Entity\Order 
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Add albums
     *
     * @param \Dusk\UserBundle\Entity\Album $albums
     * @return Room
     */
    public function addAlbum(\Dusk\UserBundle\Entity\Album $albums) {
        $this->albums[] = $albums;

        return $this;
    }

    /**
     * Remove albums
     *
     * @param \Dusk\UserBundle\Entity\Album $albums
     */
    public function removeAlbum(\Dusk\UserBundle\Entity\Album $albums) {
        $this->albums->removeElement($albums);
    }

    /**
     * Get albums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlbums() {
        return $this->albums;
    }

    public function isActiveRoom() {
        $now = new \DateTime('now');
        
        if ($this->getIsActive() and $this->getExpiredAt() >= $now) {
            return true;
        }
        
        return false;
    }

}