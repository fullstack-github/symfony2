<?php
namespace Dusk\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pages
 * @ORM\Entity
 * @ORM\Table(name="enquiry")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\EnquiryRepository")
 */
class Enquiry
{
     /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="EnquiryCategory", inversedBy="enquiry")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    protected $enquiry_category;
    
    /**
     * @ORM\Column(type="string", length=100)
     *
        * @Assert\NotBlank(message="Please enter your name.")
     *
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\Email(message="Please enter valid email.")
     * 
     */
    protected $email;
    
    /**
     * @ORM\Column(type="string", length=25)
     *
     *  
     */
    protected $phone;
    
    /**
     * @ORM\Column(type="string", length=15)
     *
     */
    protected $postcode;
    
    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(message="Please enter your message.")
     */
    protected $message;

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
     * @return Enquiry
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
     * @return Enquiry
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
     * Set phone
     *
     * @param string $phone
     * @return Enquiry
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return Enquiry
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    
        return $this;
    }

    /**
     * Get postcode
     *
     * @return string 
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Enquiry
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set enquiry_category
     *
     * @param \Dusk\UserBundle\Entity\EnquiryCategory $enquiryCategory
     * @return Enquiry
     */
    public function setEnquiryCategory(\Dusk\UserBundle\Entity\EnquiryCategory $enquiryCategory = null)
    {
        $this->enquiry_category = $enquiryCategory;
    
        return $this;
    }

    /**
     * Get enquiry_category
     *
     * @return \Dusk\UserBundle\Entity\EnquiryCategory 
     */
    public function getEnquiryCategory()
    {
        return $this->enquiry_category;
    }
}