<?php

namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Dusk\UserBundle\Entity\Album;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Pages
 * @ORM\Entity
 * @ORM\Table(name="track")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\TrackRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Track {

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
    protected $artist;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    protected $is_paid;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    protected $is_active;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * 
     */
    protected $created_at;

    public function __construct() {
        $this->is_active = true;
        $this->albums = new ArrayCollection();
        $this->created_at = new \DateTime();
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    
    /**
     * Set createdAt
     * @ORM\PrePersist()
     
     * @return Testimonial
     */
    public function setCreatedAt()
    {
        $this->created_at = new \DateTime();
    
        return $this;
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
     * Set is_active
     *
     * @param boolean $isActive
     * @return Banner
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
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {

        if (null !== $this->getFile1()) {
            // do whatever you want to generate a unique name
            $filename1 = sha1(uniqid(mt_rand(), true));
            $this->audio = $filename1 . '.' . $this->getFile1()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if ($this->getFile1()) {

            // if there is an error when moving the file, an exception will
            // be automatically thrown by move(). This will properly prevent
            // the entity from being persisted to the database on error
            $this->getFile1()->move($this->getAudioUploadRootDir(), $this->audio);

            // check if we have an old image
            if (isset($this->temp1)) {
                // delete the old image
                unlink($this->getAudioUploadRootDir() . '/' . $this->temp1);
                // clear the temp image path
                $this->temp1 = null;
            }
            $this->file1 = null;
        }
        if (null === $this->getFile1())
            return;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
        if ($file1 = $this->getAudioAbsolutePath()) {
            unlink($file1);
        }
    }

    #################### file upload code ####################

    /**
     * Set title
     *
     * @param string $title
     * @return Track
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
     * Set artist
     *
     * @param string $artist
     * @return Track
     */
    public function setArtist($artist) {
        $this->artist = $artist;

        return $this;
    }

    /**
     * Get artist
     *
     * @return string 
     */
    public function getArtist() {
        return $this->artist;
    }

    /**
     * Set is_paid
     *
     * @param boolean $isPaid
     * @return Track
     */
    public function setIsPaid($isPaid) {
        $this->is_paid = $isPaid;

        return $this;
    }

    /**
     * Get is_paid
     *
     * @return boolean 
     */
    public function getIsPaid() {
        return $this->is_paid;
    }

    
    /**
     * @ORM\ManyToMany(targetEntity="Album", inversedBy="tracks")
     * @ORM\JoinTable(name="albums_tracks",
     * joinColumns={@ORM\JoinColumn(name="track_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="album_id", referencedColumnName="id")}
     * )
     */
    protected $albums;

    #################### file upload code ####################

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $audio;

    /**
     * Set audio
     *
     * @param string $audio
     * @return Track
     */
    public function setAudio($audio) {
        $this->audio = $audio;

        return $this;
    }

    /**
     * Get audio
     *
     * @return string 
     */
    public function getAudio() {
        return $this->audio;
    }

    private $temp1;

    /**
     * @Assert\File(maxSize="500000000")
     */
    private $file1;

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile1() {
        return $this->file1;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile1(UploadedFile $file1 = null) {
        $this->file1 = $file1;
        // check if we have an old image path
        if (isset($this->audio)) {
            // store the old name to delete after the update
            $this->temp1 = $this->audio;
            $this->audio = null;
        } else {
            $this->audio = 'initial';
        }
    }

    public function getAudioAbsolutePath() {
        return null === $this->audio ? null : $this->getAudioUploadRootDir() . '/' . $this->audio;
    }

    public function getAudioWebPath() {
        return null === $this->audio ? null : $this->getAudioUploadDir() . '/' . $this->audio;
    }

    protected function getAudioUploadRootDir() {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getAudioUploadDir();
    }

    protected function getAudioUploadDir() {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/track/audio';
    }

    #################### file upload code ####################

    /**
     * Add albums
     *
     * @param \Dusk\UserBundle\Entity\Album $albums
     * @return Track
     */
    public function addAlbum(\Dusk\UserBundle\Entity\Album $albums)
    {
        $albums->addTrack($this);
        $this->albums[] = $albums;
    
        return $this;
    }

    /**
     * Remove albums
     *
     * @param \Dusk\UserBundle\Entity\Album $albums
     */
    public function removeAlbum(\Dusk\UserBundle\Entity\Album $albums)
    {
        $this->albums->removeElement($albums);
    }

    /**
     * Get albums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlbums()
    {
        return $this->albums;
    }
    
//    public function setAlbums(\Dusk\UserBundle\Entity\Album $albums) {
//        $this->albums = $albums;
//    }
}