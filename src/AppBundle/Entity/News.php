<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\FileTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="news")
 */
class News
{
    use AbstractEntityTrait;

    use FileTrait;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $publishStartDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $publishEndDate;

    /**
     * @return \DateTime
     */
    public function getPublishStartDate()
    {
        return $this->publishStartDate;
    }

    /**
     * @param \DateTime $publishStartDate
     * @return News
     */
    public function setPublishStartDate(\DateTime $publishStartDate)
    {
        $this->publishStartDate = $publishStartDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishEndDate()
    {
        return $this->publishEndDate;
    }

    /**
     * @param \DateTime $publishEndDate
     * @return News
     */
    public function setPublishEndDate(\DateTime $publishEndDate)
    {
        $this->publishEndDate = $publishEndDate;
        return $this;
    }


}
