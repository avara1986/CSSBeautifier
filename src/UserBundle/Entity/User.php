<?php

namespace UserBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
/**
 * User
 */
class User implements AdvancedUserInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var string
     */
    private $roles = "";

    /**
     * @var bool
     */
    private $isActive;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = sha1($password);

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }
    /**
     * set Roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles[0];

        return $this;
    }
    /**
     * Get Roles
     * @return multitype:string
     */
    public function getRoles()
    {
        return explode(' ', $this->roles);
    }
    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
     public function setIsActive($isActive)
     {
         $this->isActive = $isActive;

         return $this;
     }

     /**
      * Get username
      *
      * @return string
      */
     public function getIsActive()
     {
         return $this->isActive;
     }
    /**
     * Erase Credentials
     */
    public function eraseCredentials()
    {

    }
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }
}
