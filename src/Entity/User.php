<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;




/**
* @ORM\Entity(repositoryClass="App\Repository\UserRepository")
*/
class User
{
  /**
  * @ORM\Id
  * @ORM\GeneratedValue
  * @ORM\Column(type="integer")
  */
  private $id;

  /**
  * @ORM\Column(type="string",  length=25, unique=true)
  */
  private $name;

  /**
  * @ORM\Column(type="string", length=100)
  */
  private $password;

  /**
  * @ORM\Column(type="string")
  */
  private $avatar;

  /**
  * @ORM\Column(type="string", unique=true)
  */
  private $email;

  /**
  * @ORM\Column(type="string")
  */
  private $salt;


  /**
  * @ORM\Column(type="string", nullable=true)
  */
  protected $confirmationToken;     // Random string sent to the user email address in order to verify it.

  /**
  *@ORM\Column(type="datetime", nullable=true)
  */
  protected $passwordRequestedAt;

  /**
  * @ORM\Column(type="array")
  */
  protected $role;

  /**
  * @ORM\ManyToMany(targetEntity="App\Entity\Image")
  */
  protected $images;

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
  * Get the value of Password
  *
  * @return mixed
  */
  public function getPassword()
  {
    return $this->password;
  }

  /**
  * Set the value of Password
  *
  * @param mixed password
  *
  * @return self
  */
  public function setPassword($password)
  {
    $this->password = $password;

    return $this;
  }

  /**
  * Get the value of Email
  *
  * @return mixed
  */
  public function getEmail()
  {
    return $this->email;
  }

  /**
  * Set the value of Email
  *
  * @param mixed email
  *
  * @return self
  */
  public function setEmail($email)
  {
    $this->email = $email;

    return $this;
  }

  /**
  * Get the value of Salt
  *
  * @return mixed
  */
  public function getSalt()
  {
    return $this->salt;
  }

  /**
  * Set the value of Salt
  *
  * @param mixed salt
  *
  * @return self
  */
  public function setSalt($salt)
  {
    $this->salt = $salt;

    return $this;
  }

  /**
  * Get the value of Confirmation Token
  *
  * @return mixed
  */
  public function getConfirmationToken()
  {
    return $this->confirmationToken;
  }

  /**
  * Set the value of Confirmation Token
  *
  * @param mixed confirmationToken
  *
  * @return self
  */
  public function setConfirmationToken($confirmationToken)
  {
    $this->confirmationToken = $confirmationToken;

    return $this;
  }

  /**
  * Get the value of Password Requested At
  *
  * @return mixed
  */
  public function getPasswordRequestedAt()
  {
    return $this->passwordRequestedAt;
  }

  /**
  * Set the value of Password Requested At
  *
  * @param mixed passwordRequestedAt
  *
  * @return self
  */
  public function setPasswordRequestedAt($passwordRequestedAt)
  {
    $this->passwordRequestedAt = $passwordRequestedAt;

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



  /** @see \Serializable::serialize() */
  public function serialize()
  {
      return serialize(array(
          $this->id,
          $this->username,
          $this->password,
          // see section on salt below
          // $this->salt,
      ));
  }

  /** @see \Serializable::unserialize() */
  public function unserialize($serialized)
  {
      list (
          $this->id,
          $this->username,
          $this->password,
          // see section on salt below
          // $this->salt
      ) = unserialize($serialized);
  }

    /**
     * Get the value of Avatar
     *
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set the value of Avatar
     *
     * @param mixed avatar
     *
     * @return self
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }



    /**
     * Get the value of Role
     *
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the value of Role
     *
     * @param mixed role
     *
     * @return self
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

}
