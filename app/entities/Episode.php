<?php

/**
 * This file is covered by the AGPLv3 license, which can be found at the LICENSE file in the root of this project.
 * @copyright 2017 subtitulamos.tv
 */

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="episodes")
 */
class Episode
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="episodes")
     * @ORM\JoinColumn(name="show_id", referencedColumnName="id")
     */
    private $show;

    /**
     * @ORM\Column(type="integer")
     */
    private $season;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $downloads;
    
    /**
     * @ORM\OneToMany(targetEntity="Version", mappedBy="episode")
     */
    private $versions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->versions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getFullName()
    {
        return $this->show->getName() . " " .\str_pad($this->season, 2, "0", STR_PAD_LEFT) . "x" . \str_pad($this->number, 2, "0", STR_PAD_LEFT) . " - " . $this->getName();
    }
    
    /////////////////////////// AUTOGENERATED METHODS \\\\\\\\\\\\\\\\\\\\\
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
     * Set season
     *
     * @param integer $season
     *
     * @return Episode
     */
    public function setSeason($season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season
     *
     * @return integer
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return Episode
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Episode
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set downloads
     *
     * @param integer $downloads
     *
     * @return Episode
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;

        return $this;
    }

    /**
     * Get downloads
     *
     * @return integer
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * Set show
     *
     * @param \App\Entities\Show $show
     *
     * @return Episode
     */
    public function setShow(\App\Entities\Show $show = null)
    {
        $this->show = $show;

        return $this;
    }

    /**
     * Get show
     *
     * @return \App\Entities\Show
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * Add version
     *
     * @param \App\Entities\Version $version
     *
     * @return Episode
     */
    public function addVersion(\App\Entities\Version $version)
    {
        $this->versions[] = $version;

        return $this;
    }

    /**
     * Remove version
     *
     * @param \App\Entities\Version $version
     */
    public function removeVersion(\App\Entities\Version $version)
    {
        $this->versions->removeElement($version);
    }

    /**
     * Get versions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVersions()
    {
        return $this->versions;
    }

    
}
