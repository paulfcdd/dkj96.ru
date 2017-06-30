<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\FileTrait;
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
