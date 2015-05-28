<?php

namespace CrawlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Website
 */
class Css
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
    private $file;

    /**
     * @var string
     */
    private $original;

    /**
     * @var string
     */
    private $originalCompressed;

    /**
     * @var string
     */
    private $beauty;

    /**
     * @var string
     */
    private $beautyCompressed;

    /**
     * @var \CrawlerBundle\Entity\Website
     */
    private $website;

    public function __construct()
    {
        $this->updatedTimestamps();
    }
    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        //$this->setLastUpdated(new \DateTime(date('Y-m-d H:i:s')));

        if($this->getCreated() == null)
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
     * Set file
     *
     * @param string $file
     * @return Css
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Css
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
     * Set original
     *
     * @param string $original
     * @return Css
     */
    public function setOriginal($original)
    {
        $this->original = $original;

        return $this;
    }

    /**
     * Get original
     *
     * @return string 
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Set originalCompressed
     *
     * @param string $originalCompressed
     * @return Css
     */
    public function setOriginalCompressed($originalCompressed)
    {
        $this->originalCompressed = $originalCompressed;

        return $this;
    }

    /**
     * Get originalCompressed
     *
     * @return string 
     */
    public function getOriginalCompressed()
    {
        return $this->originalCompressed;
    }

    /**
     * Set beauty
     *
     * @param string $beauty
     * @return Css
     */
    public function setBeauty($beauty)
    {
        $this->beauty = $beauty;

        return $this;
    }

    /**
     * Get beauty
     *
     * @return string 
     */
    public function getBeauty()
    {
        return $this->beauty;
    }

    /**
     * Set beautyCompressed
     *
     * @param string $beautyCompressed
     * @return Css
     */
    public function setBeautyCompressed($beautyCompressed)
    {
        $this->beautyCompressed = $beautyCompressed;

        return $this;
    }

    /**
     * Get beautyCompressed
     *
     * @return string 
     */
    public function getBeautyCompressed()
    {
        return $this->beautyCompressed;
    }

    /**
     * Set website
     *
     * @param \CrawlerBundle\Entity\Website $website
     * @return Css
     */
    public function setWebsite(\CrawlerBundle\Entity\Website $website = null)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return \CrawlerBundle\Entity\Website 
     */
    public function getWebsite()
    {
        return $this->website;
    }
}
