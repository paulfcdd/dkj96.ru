<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="reviews")
 * @ORM\AttributeOverrides({
 *     @ORM\AttributeOverride(
 *          name="status",
 *          column=@ORM\Column(
 *              name="status",
 *              type="boolean"
 *          )
 *      ),
 *     @ORM\AttributeOverride(
 *          name="dateReceived",
 *          column=@ORM\Column(
 *              name="date_received",
 *              type="datetime"
 *          )
 *      ),
 * })
 */
class Review extends Feedback
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event", inversedBy="reviews")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    private $event;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }



    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param $event
     * @return $this
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }


}
