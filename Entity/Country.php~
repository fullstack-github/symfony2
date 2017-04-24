<?php

namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\CountryRepository")
 */
class Country {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code", type="string", length=255)
     */
    private $country_code;

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
     * @return Country
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
     * Set country_code
     *
     * @param string $countryCode
     * @return Country
     */
    public function setCountryCode($countryCode) {
        $this->country_code = $countryCode;

        return $this;
    }

    /**
     * Get country_code
     *
     * @return string 
     */
    public function getCountryCode() {
        return $this->country_code;
    }

    public function __toString() {
        return $this->name;
    }

    ########## PrePersist created_at and PostPersiste updated_at DateTime Object ##########

    /**
     * @ORM\column(type="datetime", nullable=false)
     * 
     */
    protected $created_at;

    /**
     * @ORM\column(type="datetime", nullable=false)
     * 
     */
    protected $updated_at;

    public function __construct() {
        $this->isActive = false;
        $this->updated_at = new \DateTime();
    }

    /**
     * Set createdAt
     * @ORM\PrePersist()

     * @return Testimonial
     */
    public function setCreatedAt() {
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

     * @ORM\preUpdate()
     * @return Testimonial
     */
    public function setUpdatedAt() {
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
}