<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\FileTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="artists")
 * @ORM\HasLifecycleCallbacks
 */
class Artist
{
    use AbstractEntityTrait;

    use FileTrait;
}