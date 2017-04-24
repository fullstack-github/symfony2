<?php

// src/Acme/UserBundle/Entity/User.php

namespace Dusk\UserBundle\Entity;

//use FOS\UserBundle\Model\User as BaseUser;
use Sonata\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="dusk_user")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $company_name;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="users")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    protected $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $address1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $address2;

    /**
     * @ORM\ManyToOne(targetEntity="State", inversedBy="users")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     */
    protected $state;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $zipcode;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $phone1;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $phone2;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    protected $isFreePeriod=true;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * Set company_name
     *
     * @param string $companyName
     * @return User
     */
    public function setCompanyName($companyName) {
        $this->company_name = $companyName;

        return $this;
    }

    /**
     * Get company_name
     *
     * @return string 
     */
    public function getCompanyName() {
        return $this->company_name;
    }

    /**
     * Set address1
     *
     * @param string $address1
     * @return User
     */
    public function setAddress1($address1) {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address1
     *
     * @return string 
     */
    public function getAddress1() {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return User
     */
    public function setAddress2($address2) {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string 
     */
    public function getAddress2() {
        return $this->address2;
    }

    /**
     * Set zipcode
     *
     * @param string $zipcode
     * @return User
     */
    public function setZipcode($zipcode) {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string 
     */
    public function getZipcode() {
        return $this->zipcode;
    }

    /**
     * Set phone1
     *
     * @param string $phone1
     * @return User
     */
    public function setPhone1($phone1) {
        $this->phone1 = $phone1;

        return $this;
    }

    /**
     * Get phone1
     *
     * @return string 
     */
    public function getPhone1() {
        return $this->phone1;
    }

    /**
     * Set phone2
     *
     * @param string $phone2
     * @return User
     */
    public function setPhone2($phone2) {
        $this->phone2 = $phone2;

        return $this;
    }

    /**
     * Get phone2
     *
     * @return string 
     */
    public function getPhone2() {
        return $this->phone2;
    }

    /**
     * Set country
     *
     * @param \Dusk\UserBundle\Entity\Country $country
     * @return User
     */
    public function setCountry(\Dusk\UserBundle\Entity\Country $country = null) {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Dusk\UserBundle\Entity\Country 
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * Set state
     *
     * @param \Dusk\UserBundle\Entity\State $state
     * @return User
     */
    public function setState(\Dusk\UserBundle\Entity\State $state = null) {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return \Dusk\UserBundle\Entity\State 
     */
    public function getState() {
        return $this->state;
    }

    ########## PrePersist created_at and PostPersiste updated_at DateTime Object ##########

    /**
     * @ORM\column(type="datetime", nullable=true)
     * 
     */
    protected $created_at;

    /**
     * @ORM\column(type="datetime", nullable=true)
     * 
     */
    protected $updated_at;

    public function __construct() {
        parent::__construct();
        $this->updated_at = new \DateTime();
        $this->roles = array('ROLE_ADMIN');
    }

    /**
     * Set createdAt
     * @ORM\PrePersist()

     * @return Testimonial
     */
    public function setCreatedAt(\DateTime $created_at = null) {
        $this->created_at = new \DateTime();

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set updated_at
     * @ORM\PreUpdate()
     * @return Testimonial
     */
    public function setUpdatedAt(\DateTime $updated_at = null) {
        $this->updated_at = new \DateTime();

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt() {
        return $this->updated_at = new \DateTime();
    }

    ########## PrePersist created_at and PostPersiste updated_at DateTime Object ##########

    /**
     * Set isFreePeriod
     *
     * @param boolean $isFreePeriod
     * @return User
     */
    public function setIsFreePeriod($isFreePeriod)
    {
        $this->isFreePeriod = $isFreePeriod;
    
        return $this;
    }

    /**
     * Get isFreePeriod
     *
     * @return boolean 
     */
    public function getIsFreePeriod()
    {
        return $this->isFreePeriod;
    }
}