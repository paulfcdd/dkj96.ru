<?php

namespace AppBundle\Listener;


use AppBundle\Entity\File;
use AppBundle\Entity\Traits\FileTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class FileListener implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents() {
        return ['postLoad'];
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs) {
        if(!in_array(FileTrait::class, class_uses($eventArgs->getObject()))) {
            return;
        }

        $result = $eventArgs->getEntityManager()->createQueryBuilder()
            ->select('f')
            ->from(File::class, 'f')
            ->where('f.foreignKey = :id')
            ->andWhere('f.entity = :entity')
            ->setParameter('id', $eventArgs->getObject()->getId())
            ->setParameter('entity', get_class($eventArgs->getObject()))
            ->getQuery()
            ->getResult();

        $eventArgs->getObject()->setFiles(new \Doctrine\Common\Collections\ArrayCollection($result));
    }
}