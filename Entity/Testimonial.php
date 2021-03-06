<?php

namespace Dusk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pages
 * @ORM\Entity
 * @ORM\Table(name="testimonial")
 * @ORM\Entity(repositoryClass="Dusk\UserBundle\Entity\TestimonialRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Testimonial {

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
    protected $name;

    /**
     * @ORM\Column(type="text")
     *
     */
    protected $message;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $company;

    
    /**
     * @ORM\Column(type="boolean", nullable=false)
     *
     */
    protected $is_active;
    
    /**
     * @ORM\column(type="datetime", nullable=true)
     * 
     */
    protected $date;
    

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
     * @return Testimonial
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
     * Set message
     *
     * @param string $message
     * @return Testimonial
     */
    public function setMessage($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage() {
        return $this->message;
    }


    /**
     * Set company
     *
     * @param string $company
     * @return Testimonial
     */
    public function setCompany($company)
    {
        $this->company = $company;
    
        return $this;
    }

    /**
     * Get company
     *
     * @return string 
     */
    public function getCompany()
    {
        return $this->company;
    }
       
    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return Testimonial
     */
    public function setIsActive($isActive=null)
    {
        $this->is_active = $isActive;
    
        return $this;
    }

    /**
     * Get is_active
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->is_active;
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
    
    public function __construct()
    {
        $this->isActive = false;
        $this->updated_at = new \DateTime();
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
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    
    /**
     * Set updated_at
     * @ORM\PreUpdate()
     * @return Testimonial
     */
    public function setUpdatedAt()
    {
        $this->updated_at = new \DateTime();
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at = new \DateTime();
    }
    ########## PrePersist created_at and PostPersiste updated_at DateTime Object ##########
    
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
        return 'uploads/testimonial';
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

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Testimonial
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
}