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
}