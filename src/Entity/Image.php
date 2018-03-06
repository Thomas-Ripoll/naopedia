<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @Vich\Uploadable
 */
class Image {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * 
     */
    private $url;

    /**
     * @Assert\NotNull(message="Une image est obligatoire pour certifier l'observation.")
     * @Vich\UploadableField(mapping="images", fileNameProperty="url")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string")
     */
    private $alt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="images")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $likes;
    
    /**
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;
    
    /**
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct() {
        $this->likes = [];
        $this->createdAt = $this->updatedAt = new \DateTime();
    }

    public function __toString() {
        return $this->getAlt();
    }

    /**
     * Get the value of Id
     *
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set the value of Id
     *
     * @param mixed id
     *
     * @return self
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }
    /**
     * 
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    public function getImageFile() {
        return $this->imageFile;
    }

    public function setUrl($image) {
        
        $this->url = $image;
        if(is_null($this->alt)){
            $this->alt = $image;
        }
        return $this;
    }

    public function setImageFile(File $imageFile = null) {
        $this->imageFile = $imageFile;
        if ($imageFile) {
            $this->updatedAt = new \DateTime('now');
        }
        return $this;
    }

    
    /**
     * Get the value of Alt
     *
     * @return mixed
     */
    public function getAlt() {
        return $this->alt;
    }

    /**
     * Set the value of Alt
     *
     * @param mixed alt
     *
     * @return self
     */
    public function setAlt($alt) {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get the value of Author
     *
     * @return mixed
     */
    public function getAuthor() {//: Category
        return $this->author;
    }

    /**
     * Set the value of Author
     *
     * @param mixed author
     *
     * @return self
     */
    public function setAuthor($author) {
        $this->author = $author;

        return $this;
    }

    public function addLike($user_id) {
        if (!in_array($user_id, $this->likes)) {
            $this->likes[] = $user_id;
        }
        return $this;
    }

    public function removeLike($user_id) {
        if (in_array($user_id, $this->likes)) {
            array_splice($this->likes, array_search($user_id, $this->likes), 1);
        }
        return $this;
    }

    public function getLikes() {
        return $this->likes;
    }
    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
        return $this;
    }


}
