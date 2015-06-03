<?php

namespace CrawlerBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Website
 */
class Website
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $url;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var string
     */
    private $token;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $css;

    public function __construct()
    {
        $this->updatedTimestamps();
        $this->css = new ArrayCollection();
    }
    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        if($this->getCreated() === null)
        {
            $this->setCreated(new \DateTime(date('Y-m-d H:i:s')));
        }
    }

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
     * Set url
     *
     * @param string $url
     * @return Website
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Website
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Website
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    /**
     * Add css
     *
     * @param \CrawlerBundle\Entity\Css $css
     * @return Website
     */
    public function addCss(\CrawlerBundle\Entity\Css $css)
    {
        $this->css[] = $css;

        return $this;
    }

    /**
     * Remove css
     *
     * @param \CrawlerBundle\Entity\Css $css
     */
    public function removeCss(\CrawlerBundle\Entity\Css $css)
    {
        $this->css->removeElement($css);
    }

    /**
     * Get css
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCss()
    {
        return $this->css;
    }
}
