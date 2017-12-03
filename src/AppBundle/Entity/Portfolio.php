<?php

namespace AppBundle\Entity;


use AppBundle\Entity\Traits\FileTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 */
class Portfolio
{
    use FileTrait;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Event", inversedBy="portfolio", orphanRemoval=true)
     */
    private $event;

    /**
     * @var string
     * @ORM\Column(unique=true)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var string
     * @ORM\Column(type="text", length=2000, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $eventDate;
       
    /**
     * @var string
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $seoTitle;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $seoKeywords;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $seoDescription;
    
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $redirect = false;
    
    /**
     * @var string
     * 
     * @ORM\Column(nullable=true)
     */ 
    private $redirectUrl;
    
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Portfolio
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
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
     * @return Portfolio
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Portfolio
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setDateCreated()
    {
        $this->dateCreated = new \DateTime();

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setEventDate()
    {
        $this->eventDate = $this->getEvent()->getEventDate();

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
     * Set event
     *
     * @param Event $event
     *
     * @return Portfolio
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \AppBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }
    
     /**
     * @return string
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * @param string | null $seoTitle
     * @return $this
     */
    public function setSeoTitle(string $seoTitle = null)
    {
        $this->seoTitle = $seoTitle;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSeoKeywords()
    {
        return $this->seoKeywords;
    }

    /**
     * @param string | null $seoKeywords
     * @return $this
     */
    public function setSeoKeywords(string $seoKeywords =  null)
    {
        $this->seoKeywords = $seoKeywords;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSeoDescription()
    {
        return $this->seoDescription;
    }

    /**
     * @param string | null $seoDescription
     * @return $this
     */
    public function setSeoDescription(string $seoDescription = null)
    {
        $this->seoDescription = $seoDescription;
        return $this;
    }
	
	/**
     * @return bool
     */
    public function isRedirect()
    {
        return $this->redirect;
    }

    /**
     * @param bool $redirect
     */
    public function setRedirect(bool $redirect)
    {
        $this->redirect = $redirect;
    }
    
    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setRedirectUrl(string $redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
        
        return $this;
	}
}
