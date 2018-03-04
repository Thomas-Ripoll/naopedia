<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
*/
class Image
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
  private $url;

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

  public function __construct() {
      $this->likes = [];
  }
  public function __toString()
  {
      return $this->getAlt();
  }

  /**
  * Get the value of Id
  *
  * @return mixed
  */
  public function getId()
  {
    return $this->id;
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
  * Get the value of Url
  *
  * @return mixed
  */
  public function getUrl()
  {
    return $this->url;
  }

  /**
  * Set the value of Url
  *
  * @param mixed url
  *
  * @return self
  */
  public function setUrl($url)
  {
    $this->url = $url;

    return $this;
  }

  /**
  * Get the value of Alt
  *
  * @return mixed
  */
  public function getAlt()
  {
    return $this->alt;
  }

  /**
  * Set the value of Alt
  *
  * @param mixed alt
  *
  * @return self
  */
  public function setAlt($alt)
  {
    $this->alt = $alt;

    return $this;
  }

  /**
  * Get the value of Author
  *
  * @return mixed
  */
  public function getAuthor()//: Category
  {
    return $this->author;
  }

  /**
  * Set the value of Author
  *
  * @param mixed author
  *
  * @return self
  */
  public function setAuthor($author)
  {
    $this->author = $author;

    return $this;
  }
  public function addLike($user_id){
      if(!in_array($user_id, $this->likes)){
          $this->likes[] =$user_id;
      }
      return $this;
  }
  public function removeLike($user_id){
      if(in_array($user_id, $this->likes)){
          array_splice( $this->likes, array_search($user_id, $this->likes), 1);
      }
      return $this;
  }
  public function getLikes(){
      return $this->likes;
  }

}
