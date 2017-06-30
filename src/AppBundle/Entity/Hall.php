<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Traits\FileTrait;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="halls")
 */
class Hall
{
    use AbstractEntityTrait;
    use FileTrait;

    /**
     * @var integer
     * @ORM\Column(type="integer", length=10)
     */
    private $capacity;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=20000)
     */
    private $specification;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Booking", mappedBy="hall")
     */
    private $bookings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * @param string $specification
     * @return Hall
     */
    public function setSpecification(string $specification)
    {
        $this->specification = $specification;
        return $this;
    }

    /**
     * Set capacity
     *
     * @param integer $capacity
     *
     * @return Hall
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return integer
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param Booking $booking
     * @return $this
     */
    public function addBooking(Booking $booking)
    {

        if(! $this->getBookings()->contains($booking)) {
            $this->getBookings()->add($booking);
            $booking->setHall($this);
        }

        return $this;
    }

    /**
     * @param ArrayCollection $bookings
     * @return $this
     */
    public function setBookings(ArrayCollection $bookings) {
        $this->bookings = $bookings;

        return $this;
    }

    /**
     * Remove booking
     *
     * @param \AppBundle\Entity\Booking $booking
     */
    public function removeBooking(\AppBundle\Entity\Booking $booking)
    {
        $this->bookings->removeElement($booking);
    }

    /**
     * @return ArrayCollection
     */
    public function getBookings()
    {
        return $this->bookings;
    }
}
