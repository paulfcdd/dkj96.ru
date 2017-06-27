<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class History
 * @package AppBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="history")
 * @ORM\HasLifecycleCallbacks
 */
class History
{
    use AbstractEntityTrait;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isEnabled;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
     */
    public function setEnabled(bool $isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }
}