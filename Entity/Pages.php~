<?php
namespace Dusk\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pages
 * @ORM\Entity
 * @ORM\Table(name="pages")
 */
class Pages
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
     * @ORM\Column(type="string", length=255)
     *
     */
    protected $pageroute;
    
    /**
     * @ORM\Column(type="string", length=255)
     *
     *
     */
    protected $pagename;
    
    /**
     * @ORM\Column(type="text")
     *
     */
    protected $pagecontent;
    
    

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
     * Set pageroute
     *
     * @param string $pageroute
     * @return Pages
     */
    public function setPageroute($pageroute)
    {
        $this->pageroute = $pageroute;
    
        return $this;
    }

    /**
     * Get pageroute
     *
     * @return string 
     */
    public function getPageroute()
    {
        return $this->pageroute;
    }

    /**
     * Set pagename
     *
     * @param string $pagename
     * @return Pages
     */
    public function setPagename($pagename)
    {
        $this->pagename = $pagename;
    
        return $this;
    }

    /**
     * Get pagename
     *
     * @return string 
     */
    public function getPagename()
    {
        return $this->pagename;
    }

    /**
     * Set pagecontent
     *
     * @param string $pagecontent
     * @return Pages
     */
    public function setPagecontent($pagecontent)
    {
        $this->pagecontent = $pagecontent;
    
        return $this;
    }

    /**
     * Get pagecontent
     *
     * @return string 
     */
    public function getPagecontent()
    {
        return $this->pagecontent;
    }
}