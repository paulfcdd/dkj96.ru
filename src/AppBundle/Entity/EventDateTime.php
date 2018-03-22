<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="events_date_time")
 * @ORM\Entity()
 */
class EventDateTime
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event", inversedBy="eventDateTime", cascade={"persist"})
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    private $event;

    /**
     * @var string $eventDate
     *
     * @ORM\Column()
     */
    private $date;

    /**
     * @var string $eventTime
     *
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @var string | null $kassyRuPID
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $kassyRuPID;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     * @return EventDateTime
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return string
     */
    public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * @param string $date
     * @return EventDateTime
     */
    public function setDate(string $date): EventDateTime
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getTime(): ?string
    {
        return $this->time;
    }

    /**
     * @param string $time
     * @return EventDateTime
     */
    public function setTime(string $time): EventDateTime
    {
        $this->time = $time;
        return $this;
    }



    /**
     * @return string
     */
    public function getKassyRuPID()
    {
        return $this->kassyRuPID;
    }

    /**
     * @param string $kassyRuPID
     * @return EventDateTime
     */
    public function setKassyRuPID($kassyRuPID)
    {
        $this->kassyRuPID = $kassyRuPID;
        return $this;
    }


}