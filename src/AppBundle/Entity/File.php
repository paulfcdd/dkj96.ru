<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="file")
 * @ORM\HasLifecycleCallbacks()
 */

class File
{
    const MIME_TYPE_ICON_MAP = [
        'image' => 'fa-file-image-o',
        'audio' => 'fa-file-audio-o',
        'video' => 'fa-file-video-o',
        'application/pdf' => 'fa-file-pdf-o',
        'text/plain' => 'fa-file-text-o',
        'text/html' => 'fa-file-code-o',
        'application/json' => 'fa-file-code-o',
        'application/gzip' => 'fa-file-archive-o',
        'application/zip' => 'fa-file-archive-o',
        'application/octet-stream' => 'fa-file-o',
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(length=100, type="string", unique=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(length=100, type="string")
     */
    private $entity;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", length=11)
     */
    private $foreignKey;

    /**
     * @var string
     * @ORM\Column(name="mime_type", length=100, type="string")
     */
    private $mimeType;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $isDefault = 0;

    /**
     * @return bool
     */
    public function isIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     * @return File
     */
    public function setIsDefault(bool $isDefault)
    {
        $this->isDefault = $isDefault;
        return $this;
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
     * Set name
     *
     * @param string $name
     *
     * @return File
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
     * Set entity
     *
     * @param string $entity
     *
     * @return File
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set foreignKey
     *
     * @param integer $foreignKey
     *
     * @return File
     */
    public function setForeignKey($foreignKey)
    {
        $this->foreignKey = $foreignKey;

        return $this;
    }

    /**
     * Get foreignKey
     *
     * @return integer
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     * @return $this
     */
    public function setMimeType(string $mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @param $mimeType
     * @return mixed|string
     */
    public function guessIconByMime($mimeType) {

        if (array_key_exists($mimeType, self::MIME_TYPE_ICON_MAP)) {
            return self::MIME_TYPE_ICON_MAP[$mimeType];
        } else {
            return 'fa-question-circle-o';
        }
    }
}
