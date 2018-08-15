<?php

/**
 * This file is covered by the AGPLv3 license, which can be found at the LICENSE file in the root of this project.
 * @copyright 2017-2018 subtitulamos.tv
 */

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sequences")
 */
class Sequence implements \JsonSerializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Subtitle", inversedBy="sequences")
     * @ORM\JoinColumn(name="subtitle_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $subtitle;

    /**
     * @ORM\Column(type="integer")
     */
    private $revision;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @ORM\Column(type="integer", name="start_time")
     */
    private $startTime;

    /**
     * @ORM\Column(type="integer", name="end_time")
     */
    private $endTime;

    /**
     * @ORM\Column(type="text", length=255)
     */
    private $text;

    /**
     * @ORM\Column(type="boolean")
     */
    private $locked;

    /**
     * @ORM\Column(type="boolean")
     */
    private $verified;

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'tstart' => $this->startTime,
            'tend' => $this->endTime,
            'locked' => $this->locked,
            'verified' => $this->verified,
            'text' => $this->text,
            'author' => $this->author ? $this->author->getId() : 0
        ];
    }

    public function __clone()
    {
        $this->id = 0;
    }

    /**
     * Increment the revision of this sequence by 1
     *
     * @return void
     */
    public function incRevision()
    {
        $this->revision++;
    }

    ///////////////////////////////// AUTOGENERATED! \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

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
     * Set revision
     *
     * @param integer $revision
     *
     * @return Sequence
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * Get revision
     *
     * @return integer
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return Sequence
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
     * Set text
     *
     * @param string $text
     *
     * @return Sequence
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     *
     * @return Sequence
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set verified
     *
     * @param boolean $verified
     *
     * @return Sequence
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * Get verified
     *
     * @return boolean
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * Set subtitle
     *
     * @param \App\Entities\Subtitle $subtitle
     *
     * @return Sequence
     */
    public function setSubtitle(\App\Entities\Subtitle $subtitle = null)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get subtitle
     *
     * @return \App\Entities\Subtitle
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set startTime
     *
     * @param integer $startTime
     *
     * @return Sequence
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return integer
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param integer $endTime
     *
     * @return Sequence
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return integer
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set author
     *
     * @param \App\Entities\User $author
     * @return Sequence
     */
    public function setAuthor(\App\Entities\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \App\Entities\User
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
