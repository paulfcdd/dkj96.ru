<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="artists")
 * @ORM\HasLifecycleCallbacks
 */
class Artist
{
    use AbstractEntityTrait;
}