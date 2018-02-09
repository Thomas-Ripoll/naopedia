<?php

namespace App\Entity;

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
  * @ORM\Column(type="string", length=200)
  */
  private $alt;

  /**
  * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="images")
  * @ORM\JoinColumn(nullable=true)
  */
  private $author;

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


}
