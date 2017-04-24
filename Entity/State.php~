<?php

namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @ORM\Table(name="state")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\StateRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class State
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="countrystate")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="state_name", type="string", length=255)
     */
    private $state_name;
    
     /**
     * @var \DateTime 
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime")
     * 
     */
    private $updatedAt;
   

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
     * Set createdAt
     * @ORM\PrePersist 
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();  
        $this->updatedAt = new \DateTime();  
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();  
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set country
     *
     * @param \Dusk\UserBundle\Entity\Country $country
     * @return State
     */
    public function setCountry(\Dusk\UserBundle\Entity\Country $country = null)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return \Dusk\UserBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }
    
    public function __toString() {
        return $this->state_name;
    }


    /**
     * Set state_name
     *
     * @param string $stateName
     * @return State
     */
    public function setStateName($stateName)
    {
        $this->state_name = $stateName;
    
        return $this;
    }

    /**
     * Get state_name
     *
     * @return string 
     */
    public function getStateName()
    {
        return $this->state_name;
    }
}