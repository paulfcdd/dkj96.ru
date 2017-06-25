<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="bookings")
 */
class Booking
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
     * @var string
     * @ORM\Column()
     */
    private $phone;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var string
     * @ORM\Column()
     */
    private $email;

    /**
     * @var integer
     * @ORM\Column(type="integer", length=10)
     */
    private $guests;

    /**
     * @var string
     * @ORM\Column(length=2000)
     */
    private $about;

    /**
     * @ORM\ManyToOne(targetEntity="Hall")
     * @ORM\JoinColumn(name="hall_id", referencedColumnName="id")
     */
    private $hall;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $dateReceived;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $status = 0;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $booked = 0;

    /**
     * @return bool
     */
    public function isBooked()
    {
        return $this->booked;
    }

    /**
     * @param bool $booked
     * @return $this
     */
    public function setBooked(bool $booked)
    {
        $this->booked = $booked;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return Booking
     */
    public function setStatus(bool $status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateReceived()
    {
        return $this->dateReceived;
    }

    /**
     * @ORM\PrePersist
     */
    public function setDateReceived()
    {
        $this->dateReceived = new \DateTime();
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
     * Set phone
     *
     * @param string $phone
     *
     * @return Booking
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Booking
     */
    public function setDate($date)
    {
        $this->date = \DateTime::createFromFormat('d-m-Y', $date);

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Booking
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set guests
     *
     * @param integer $guests
     *
     * @return Booking
     */
    public function setGuests($guests)
    {
        $this->guests = $guests;

        return $this;
    }

    /**
     * Get guests
     *
     * @return integer
     */
    public function getGuests()
    {
        return $this->guests;
    }

    /**
     * Set about
     *
     * @param string $about
     *
     * @return Booking
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set hall
     *
     * @param Hall $hall
     *
     * @return Booking
     */
    public function setHall(Hall $hall = null)
    {
        $this->hall = $hall;

        return $this;
    }

    /**
     * Get hall
     *
     * @return Hall
     */
    public function getHall()
    {
        return $this->hall;
    }
}
