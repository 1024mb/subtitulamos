<?php

/**
 * This file is covered by the AGPLv3 license, which can be found at the LICENSE file in the root of this project.
 * @copyright 2017-2018 subtitulamos.tv
 */

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="alert_comments")
 */
class AlertComment
{
    /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Alert")
     * @ORM\JoinColumn(name="alert_id", referencedColumnName="id")
     */
    private $alert;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="by_user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $text;

    /**
    * @ORM\Column(type="integer")
    */
    private $type;

    /**
     * @ORM\Column(type="datetime", name="create_time", options={"default": 0})
     */
    private $creationTime;

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
     * Set text
     *
     * @param string $text
     *
     * @return AlertComment
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
     * Set type
     *
     * @param integer $type
     *
     * @return AlertComment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set alert
     *
     * @param \App\Entities\Alert $alert
     *
     * @return AlertComment
     */
    public function setAlert(\App\Entities\Alert $alert = null)
    {
        $this->alert = $alert;

        return $this;
    }

    /**
     * Get alert
     *
     * @return \App\Entities\Alert
     */
    public function getAlert()
    {
        return $this->alert;
    }

    /**
     * Set user
     *
     * @param \App\Entities\User $user
     *
     * @return AlertComment
     */
    public function setUser(\App\Entities\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entities\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set creationTime
     *
     * @param \DateTime $creationTime
     *
     * @return AlertComment
     */
    public function setCreationTime($creationTime)
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    /**
     * Get creationTime
     *
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }
}
