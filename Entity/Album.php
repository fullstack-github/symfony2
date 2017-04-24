<?php

namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Dusk\UserBundle\Entity\Room;
use Dusk\UserBundle\Entity\Track;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pages
 * @ORM\Entity
 * @ORM\Table(name="album")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\AlbumRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Album {

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
     * @ORM\Column(type="string", length=255)
     *
     */
    protected $author;
    
    /**
     * @ORM\Column(type="string", length=255, name="description")
     *
     */
    protected $desc;

    /**
     * @ORM\Column(name="no_of_track", type="integer", length=4)
     *
     */
    protected $no_of_track;

    /**
     * @var \DateTime
     * @ORM\Column(name="released_date", type="datetime")
     * 
     */
    private $released_date;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    protected $is_active;

    /**
     * @ORM\ManyToMany(targetEntity="Track", mappedBy="albums")
     */
    protected $tracks;
    
    /**
     * @ORM\ManyToMany(targetEntity="Dusk\UserBundle\Entity\Room", mappedBy="albums")
     */
    protected $rooms;
    

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
     * Set author
     *
     * @param string $author
     * @return Album
     */
    public function setAuthor($author) {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Set no_of_track
     *
     * @param integer $noOfTrack
     * @return Album
     */
    public function setNoOfTrack($noOfTrack) {
        $this->no_of_track = $noOfTrack;

        return $this;
    }

    /**
     * Get no_of_track
     *
     * @return integer 
     */
    public function getNoOfTrack() {
        return $this->no_of_track;
    }

    /**
     * Set released_date
     *
     * @param \DateTime $releasedDate
     * @return Album
     */
    public function setReleasedDate($releasedDate) {
        $this->released_date = $releasedDate;

        return $this;
    }

    /**
     * Get released_date
     *
     * @return \DateTime 
     */
    public function getReleasedDate() {
        return $this->released_date;
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
        return 'uploads/album';
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
            @unlink($this->getUploadRootDir() . '/' . $this->temp);
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

    public function __toString() {
        return $this->title ? $this->title : '';
    }

    /**
     * Add tracks
     *
     * @param \Dusk\UserBundle\Entity\Track $tracks
     * @return Album
     */
    public function addTrack(\Dusk\UserBundle\Entity\Track $tracks) {
//        $tracks->addAlbum($this);
        $this->tracks[] = $tracks;

        return $this;
    }

    /**
     * Remove tracks
     *
     * @param \Dusk\UserBundle\Entity\Track $tracks
     */
    public function removeTrack(\Dusk\UserBundle\Entity\Track $tracks) {
        $this->tracks->removeElement($tracks);
    }

    /**
     * Get tracks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTracks() {
        return $this->tracks;
    }
    
//    public function setTracks(\Dusk\UserBundle\Entity\Track $tracks) {
//        $this->tracks = $tracks;
//    }


    /**
     * Add rooms
     *
     * @param \Dusk\UserBundle\Entity\Room $rooms
     * @return Album
     */
    public function addRoom(\Dusk\UserBundle\Entity\Room $rooms)
    {
        $this->rooms[] = $rooms;
    
        return $this;
    }

    /**
     * Remove rooms
     *
     * @param \Dusk\UserBundle\Entity\Room $rooms
     */
    public function removeRoom(\Dusk\UserBundle\Entity\Room $rooms)
    {
        $this->rooms->removeElement($rooms);
    }

    /**
     * Get rooms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * Set desc
     *
     * @param string $desc
     * @return Album
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    
        return $this;
    }

    /**
     * Get desc
     *
     * @return string 
     */
    public function getDesc()
    {
        return $this->desc;
    }
}