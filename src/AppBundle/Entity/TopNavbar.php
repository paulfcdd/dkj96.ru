<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 *@ORM\Entity()
 */
class TopNavbar
{
    const ICONS = [
        'YouTube' => 'fa-youtube-play',
        'Instagram' => 'fa-instagram',
        'Facebook' => 'fa-facebook-official',
        'Одноклассники' => 'fa-odnoklassniki',
        'ВКонтакте' => 'fa-vk',

    ];

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
     * @ORM\Column(type="boolean", nullable=true)
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
     * @ORM\Column()
     */
    protected $icon;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    protected $content;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $url;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


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

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return TopNavbar
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }



}