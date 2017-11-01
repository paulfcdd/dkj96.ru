<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


trait AbstractEntityTrait {

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text", length=2000, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="text", length=20000)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateUpdated;
    
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
     * @var string
     * 
     * @ORM\Column(nullable=true)
     */ 
    private $slug;
    
    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
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
     * @return $this
     */
    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;
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
     * @ORM\PrePersist
     */
    public function setDateCreated()
    {
        $this->dateCreated = new \DateTime('now');
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setDateUpdated()
    {
        $this->dateUpdated = new \DateTime('now');
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
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
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
     * 
     * @return $this
     */
    public function setRedirect(bool $redirect)
    {
        $this->redirect = $redirect;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     * @return $this
     */
    public function setRedirectUrl(string $redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
        
        return $this;
	} 
	
	/**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
        
        return $this;
	}   
}
