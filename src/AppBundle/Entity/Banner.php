<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\FileTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="banners")
 * @ORM\Entity()
 */
class Banner
{
    use FileTrait;

    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $alt;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $category;

    /**
     * @var string | News | Event
     *
     * @ORM\Column(nullable=true)
     */
    private $object;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var boolean
     *
     * @ORM\Column()
     */
    private $link = 0;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     *
     * @return Banner
     */
    public function setAlt(string $alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     *
     * @return Banner
     */
    public function setCategory(string $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param string $object
     *
     * @return Banner
     */
    public function setObject(string $object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLink()
    {
        return $this->link;
    }

    /**
     * @param bool $link
     */
    public function setLink(bool $link)
    {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     * @return Banner
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

}