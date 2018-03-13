<?php

namespace App\Entity;
use App\Entity\Image;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
* @ORM\Entity(repositoryClass="App\Repository\BirdRepository")
*/
class Bird
{
  /**
  * @ORM\Id
  * @ORM\GeneratedValue
  * @ORM\Column(type="integer")
  */
  private $id;
  
  /**
  * @ORM\Column(type="string")
  */
  private $name;
  
  /**
  * @ORM\Column(type="string", nullable=true)
  */
  private $latinName;
  
  /**
  * @ORM\Column(type="string")
  */
  private $ordre;
  
  /**
  * @ORM\Column(type="string")
  */
  private $famille;
  
  /**
  * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="bird", cascade={"persist"})
  */
  private $images;
  
  /**
  * @ORM\Column(type="string", nullable=true)

  */
  private $description;
  
  /**
  * @ORM\Column(type="boolean", nullable=true)
  */
  private $descriptionValid;
  
  /**
  * @ORM\ManyToOne(targetEntity="App\Entity\User")
  */
  private $contributor;
  
  
  
  /**
  * @Gedmo\Slug(fields={"name", "latinName"})
  * @ORM\Column(length=128, unique=true)
  */
  private $slug;
  
  
  /**
  * Get the value of Id
  *
  * @return mixed
  */
  public function getId()
  {
    return $this->id;
  }
  
  public function __toString()
  {
    return $this->getName();
  }
  
  /**
  * Set the value of Id
  *
  * @param mixed id
  *
  * @return self
  */
  public function setId($id)
  {
    $this->id = $id;
    
    return $this;
  }
  
  /**
  * Get the value of Name
  *
  * @return mixed
  */
  public function getName()
  {
    return $this->name;
  }
  
  /**
  * Set the value of Name
  *
  * @param mixed name
  *
  * @return self
  */
  public function setName($name)
  {
    $this->name = $name;
    
    return $this;
  }
  
  /**
  * Get the value of Latin Name
  *
  * @return mixed
  */
  public function getLatinName()
  {
    return $this->latinName;
  }
  
  /**
  * Set the value of Latin Name
  *
  * @param mixed latinName
  *
  * @return self
  */
  public function setLatinName($latinName)
  {
    $this->latinName = $latinName;
    
    return $this;
  }
  
  /**
  * Get the value of Ordre
  *
  * @return mixed
  */
  public function getOrdre()
  {
    return $this->ordre;
  }
  
  /**
  * Set the value of Ordre
  *
  * @param mixed ordre
  *
  * @return self
  */
  public function setOrdre($ordre)
  {
    $this->ordre = $ordre;
    
    return $this;
  }
  
  /**
  * Get the value of Famille
  *
  * @return mixed
  */
  public function getFamille()
  {
    return $this->famille;
  }
  
  /**
  * Set the value of Famille
  *
  * @param mixed famille
  *
  * @return self
  */
  public function setFamille($famille)
  {
    $this->famille = $famille;
    
    return $this;
  }
  
  
  /**
  * Get the value of Images
  *
  * @return mixed
  */
  public function getImages()
  {
    return $this->images;
  }
  
  /**
  * Set the value of Images
  *
  * @param mixed images
  *
  * @return self
  */
  public function setImages($images)
  {
    $this->images = $images;
    
    return $this;
  }
  
  public function addImage(Image $image)
  {
    $this->images[] = $image;
    $image->setBird($this);
    return $this;
  }
  
  
  /**
  * Get the value of Description
  *
  * @return mixed
  */
  public function getDescription()
  {
    return $this->description;
  }
  
  /**
  * Set the value of Description
  *
  * @param mixed description
  *
  * @return self
  */
  public function setDescription($description)
  {
    $this->description = $description;
    
    return $this;
  }
  
  /**
  * Get the value of Contributor
  *
  * @return mixed
  */
  public function getContributor()
  {
    return $this->contributor;
  }
  
  /**
  * Set the value of Contributor
  *
  * @param mixed contributor
  *
  * @return self
  */
  public function setContributor($contributor)
  {
    $this->contributor = $contributor;
    
    return $this;
  }
  
  
  /**
  * Get the value of Slug
  *
  * @return mixed
  */
  public function getSlug()
  {
    return $this->slug;
  }
  
  /**
  * Set the value of Slug
  *
  * @param mixed slug
  *
  * @return self
  */
  public function setSlug($slug)
  {
    $this->slug = $slug;
    
    return $this;
  }
  
  
  /**
  * Get the value of descriptionValid
  */ 
  public function getDescriptionValid()
  {
    return $this->descriptionValid;
  }
  
  /**
  * Set the value of descriptionValid
  *
  * @return  self
  */ 
  public function setDescriptionValid($descriptionValid)
  {
    $this->descriptionValid = $descriptionValid;
    
    return $this;
  }
}
