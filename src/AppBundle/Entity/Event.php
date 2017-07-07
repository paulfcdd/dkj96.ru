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
     * @var int
     *
     * @ORM\Column(type="integer", length=10)
     */
    private $price;

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     * @return $this
     */
    public function setPrice(int $price)
    {
        $this->price = $price;

        return $this;
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
