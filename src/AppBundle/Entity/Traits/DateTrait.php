<?php

namespace AppBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks()
 */
trait DateTrait {

    /**
     * @var \DateTime
	 * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime
	 * @ORM\Column(type="datetime", nullable=true)
	 */
    private $dateUpdated;

    /**
     * @var \DateTime
	 * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateRemoved;

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @ORM\PrePersist()
	 * @return $this
     */
    public function setDateCreated()
    {
        $this->dateCreated = new \DateTime();

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function setDateUpdated()
    {
        $this->dateUpdated = new \DateTime();

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateRemoved()
    {
        return $this->dateRemoved;
    }

    /**
     * @param \DateTime $dateRemoved
	 * @return $this
     */
    public function setDateRemoved($dateRemoved)
    {
        $this->dateRemoved = $dateRemoved;

        return $this;
    }


}
