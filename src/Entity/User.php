<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;




/**
* @ORM\Entity(repositoryClass="App\Repository\UserRepository")
*/
class User  implements UserInterface
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
  private $username;

  /**
  * @ORM\Column(type="string")
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
  protected $roles;

  /**
  * @ORM\OneToMany(targetEntity="App\Entity\Image",mappedBy="author")
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

  public function eraseCredentials()
{
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

  public function __construct()
      {
          $this->images = new ArrayCollection();
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
     * Get the value of Username
     *
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of Username
     *
     * @param mixed username
     *
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of Roles
     *
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set the value of Roles
     *
     * @param mixed roles
     *
     * @return self
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

}
