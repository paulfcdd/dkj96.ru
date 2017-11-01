<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\FileTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class NearestEvents
 * @package AppBundle\Entity
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="events")
 */
class Event
{
    use AbstractEntityTrait;
    use FileTrait;

    /**
     * @var \DateTime $eventDate
     *
     * @ORM\Column(type="datetime")
     */
    private $eventDate;

    /**
     * @var \DateTime $eventTime
     *
     * @ORM\Column(type="time")
     */
    private $eventTime;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Review", mappedBy="event", cascade={"remove"})
     */
    private $reviews;

    /**
     * @var string | null
     *
     * @ORM\Column(nullable=true)
     */
    private $price = null;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    private $ticketUrl;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Portfolio", mappedBy="event")
     */
    private $portfolio;

    /**
     * @return Portfolio
     */
    public function getPortfolio()
    {
        return $this->portfolio;
    }

    /**
     * @param Portfolio $portfolio
     */
    public function setPortfolio(Portfolio $portfolio)
    {
        $this->portfolio = $portfolio;
    }

    /**
     * @return string
     */
    public function getTicketUrl()
    {
        return $this->ticketUrl;
    }

    /**
     * @param string $ticketUrl
     * @return Event
     */
    public function setTicketUrl(string $ticketUrl)
    {
        $this->ticketUrl = $ticketUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice(string $price)
    {
        $this->price = $price;
    }

    /**
     * @return ArrayCollection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param ArrayCollection $reviews
     */
    public function setReviews(ArrayCollection $reviews)
    {
        $this->reviews = $reviews;
    }

    /**
     * Set eventDate
     *
     * @param \DateTime $eventDate
     *
     * @return Event
     */
    public function setEventDate($eventDate)
    {
        $this->eventDate = $eventDate;

        return $this;
    }

    /**
     * Get eventDate
     *
     * @return \DateTime
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }

    /**
     * Set eventTime
     *
     * @param \DateTime $eventTime
     *
     * @return Event
     */
    public function setEventTime($eventTime)
    {
        $this->eventTime = $eventTime;

        return $this;
    }

    /**
     * Get eventTime
     *
     * @return \DateTime
     */
    public function getEventTime()
    {
        return $this->eventTime;
    }
}
