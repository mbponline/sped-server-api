<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\MessageRepository")
 */
class Message
{
   /**
    * @var int
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

   /**
    * @var int
    *
    * @ORM\Column(name="sent_to_id", type="integer")
    */
    private $sentToId;

   /**
    * @var int
    *
    * @ORM\Column(name="sent_from_id", type="integer")
    */
    private $sentFromId;

   /**
    * @var string
    *
    * @ORM\Column(name="text_message", type="string", length=10000)
    */
    private $textMessage;

   /**
    * @var int
    *
    * @ORM\Column(name="status", type="integer")
    */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="created_at", type="string", length=255)
     */
    private $createdAt;


   /**
    * Get id
    *
    * @return int
    */
    public function getId()
    {
        return $this->id;
    }

   /**
    * Set textMessage
    *
    * @param string $textMessage
    *
    * @return textMessage
    */
    public function setTextMessage($textMessage)
    {
        $this->textMessage = $textMessage;

        return $this;
    }

   /**
    * Get message
    *
    * @return string
    */
    public function getTextMessage()
    {
        return $this->textMessage;
    }

   /**
    * Set status
    *
    * @param integer $status
    *
    * @return File
    */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

   /**
    * Get status
    *
    * @return int
    */
    public function getStatus()
    {
        return $this->status;
    }

   /**
    * Set createdAt
    *
    * @param string $createdAt
    *
    * @return File
    */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

   /**
    * Get createdAt
    *
    * @return string
    */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

   /**
    * Set sentToId
    *
    * @param integer $sentToId
    *
    * @return File
    */
    public function setSentToId($sentToId)
    {
        $this->sentToId = $sentToId;

        return $this;
    }

   /**
    * Get sentToId
    *
    * @return int
    */
    public function getSentToId()
    {
        return $this->sentToId;
    }

   /**
    * Set sentFromId
    *
    * @param integer $sentFromId
    *
    * @return File
    */
    public function setSentFromId($sentFromId)
    {
        $this->sentFromId = $sentFromId;

        return $this;
    }

   /**
    * Get sentFromId
    *
    * @return int
    */
    public function getSentFromId()
    {
        return $this->sentFromId;
    }
}
