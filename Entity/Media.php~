<?php

namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Dusk\UserBundle\Entity\MediaFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
/**
 * Pages
 * @ORM\Entity
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\MediaRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Media {

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
    protected $title;
    
    /**
     * @ORM\Column(type="text")
     *
     */
    protected $description;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    protected $is_active;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="released_date", type="datetime")
     * 
     */
    protected $released_date;

    public function __construct() {
        $this->files = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Album
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
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
    
    /**
     * @ORM\OneToMany(targetEntity="MediaFile", mappedBy="media", cascade={"persist"})
     */
    protected $files;
    
    public function setFiles(ArrayCollection $files)
    {
        $this->files = $files;
    }


    /**
     * Add files
     *
     * @param \Dusk\UserBundle\Entity\MediaFile $files
     * @return Media
     */
    public function addFile(\Dusk\UserBundle\Entity\MediaFile $files)
    {
        $files->setMedia($this);
        $this->files->add($files);
    
        return $this;
    }

    /**
     * Remove files
     *
     * @param \Dusk\UserBundle\Entity\MediaFile $files
     */
    public function removeFile(\Dusk\UserBundle\Entity\MediaFile $files)
    {
        $files->setMedia(null);
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }
    
    public function __toString() {
        return $this->title ? $this->title : '';
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Media
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set released_date
     *
     * @param \DateTime $releasedDate
     * @return Media
     */
    public function setReleasedDate($releasedDate)
    {
        $this->released_date = $releasedDate;
    
        return $this;
    }

    /**
     * Get released_date
     *
     * @return \DateTime 
     */
    public function getReleasedDate()
    {
        return $this->released_date;
    }
    
        #################### file upload code ####################

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $image;

    /**
     * Set image
     *
     * @param string $image
     * @return Testimonial
     */
    public function setImage($image) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage() {
        return $this->image;
    }

    private $temp;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null) {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->image)) {
            // store the old name to delete after the update
            $this->temp = $this->image;
            $this->image = null;
        } else {
            $this->image = 'initial';
        }
    }

    public function getAbsolutePath() {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->image;
    }

    public function getWebPath() {
        return null === $this->image ? null : $this->getUploadDir() . '/' . $this->image;
    }

    protected function getUploadRootDir() {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir() {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/media';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->image = $filename . '.' . $this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->image);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir() . '/' . $this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    #################### file upload code ####################

}