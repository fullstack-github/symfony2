<?php

namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Pages
 * @ORM\Entity
 * @ORM\Table(name="csv_trak")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\CsvTrackRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CsvTrack {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    #################### file upload code ####################

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $csv;

    /**
     * Set csv
     *
     * @param string $csv
     * @return Testimonial
     */
    public function setCsv($csv) {
        $this->csv = $csv;

        return $this;
    }

    /**
     * Get csv
     *
     * @return string 
     */
    public function getCsv() {
        return $this->csv;
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
        // check if we have an old csv path
        if (isset($this->csv)) {
            // store the old name to delete after the update
            $this->temp = $this->csv;
            $this->csv = null;
        } else {
            $this->csv = 'initial';
        }
    }

    public function getAbsolutePath() {
        return null === $this->csv ? null : $this->getUploadRootDir() . '/' . $this->csv;
    }

    public function getWebPath() {
        return null === $this->csv ? null : $this->getUploadDir() . '/' . $this->csv;
    }

    protected function getUploadRootDir() {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir() {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/csv in the view.
        return 'uploads/csv';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->csv = $filename . '.' . $this->getFile()->guessExtension();
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
        $this->getFile()->move($this->getUploadRootDir(), $this->csv);

        // check if we have an old csv
        if (isset($this->temp)) {
            // delete the old csv
            unlink($this->getUploadRootDir() . '/' . $this->temp);
            // clear the temp csv path
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

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}