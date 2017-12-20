<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 *@ORM\Entity()
 */
class TopNavbar
{
    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $isLink;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected $sortOrder;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    protected $icon;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    protected $content;

    /**
     * @return bool
     */
    public function isLink()
    {
        return $this->isLink;
    }

    /**
     * @param bool $isLink
     * @return TopNavbar
     */
    public function setIsLink(bool $isLink)
    {
        $this->isLink = $isLink;
        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param int $sortOrder
     * @return TopNavbar
     */
    public function setSortOrder(int $sortOrder)
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return TopNavbar
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return TopNavbar
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return TopNavbar
     */
    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }


}