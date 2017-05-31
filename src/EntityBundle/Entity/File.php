<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\FileRepository")
 */
class File
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
     * @ORM\Column(name="message_id", type="integer")
     */
    private $messageId;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255)
     */
    private $fileName;


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
     * Set messageId
     *
     * @param integer $messageId
     *
     * @return File
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * Get messageId
     *
     * @return int
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return File
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

}
