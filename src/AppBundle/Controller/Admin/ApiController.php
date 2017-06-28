<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\File;
use AppBundle\Entity\Hall;
use AppBundle\Entity\News;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Booking;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Class ApiController
 * @package AppBundle\Controller\Admin
 * @Route("/admin/api")
 * @Method({"POST"})
 */
class ApiController extends AdminController
{

    const ENTITY_NAMESPACE_PATTERN = 'AppBundle\\Entity\\';

    /**
     * @param string|null $name
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    public function doctrineManager(string $name = null) {
        return $this->getDoctrine()->getManager($name);
    }

    /**
     * @return JsonResponse
     * @Route("/mark_as_unread/{id}/{entity}", name="admin.api.message_unread")
     */
    public function markAsUnreadAjaxAction($id, $entity) {

        $entityRepository = $this->getEntityRepository($entity)->findOneById($id);

        $entityRepository->setStatus(0);

        try {
            $this->doctrineManager()->flush();
            return JsonResponse::create('ok');
        } catch (DBALException $exception) {
            return JsonResponse::create('not ok', 500);
        }
    }

    /**
     * @Route("/object_delete/{object}/{id}", name="admin.api.object_delete")
     */
    public function deleteObjectAjaxAction($object, $id) {

        $objectClass = 'AppBundle\\Entity\\'.ucfirst($object);

        $objectEntity = $this->doctrineManager()->getRepository($objectClass)->findOneById($id);

        if ($objectEntity instanceof Hall) {

            /** @var EntityManager $em */
            $em = $this->doctrineManager()->getRepository(Booking::class);

            $qb = $em->createQueryBuilder('b')
                ->delete('AppBundle:Booking', 'b')
                ->where('b.hall = :hall')
                ->setParameter('hall', $objectEntity->getId())
                ->getQuery();

            $qb->getResult();
        }


        if ($this->deleteObjectRelatedFiles($objectClass, intval($id))) {
            $this->doctrineManager()->remove($objectEntity);
            $this->doctrineManager()->flush();
            return JsonResponse::create($objectClass);
        }


    }

    /**
     * @param $entity
     * @param $id
     * @return JsonResponse
     * @Route("/message_delete/{entity}/{id}", name="admin.api.message_delete")
     */
    public function deleteMessageAjaxAction($entity, $id) {

        $entityRepository = $this->getEntityRepository($entity)->findOneById($id);

        $this->doctrineManager()->remove($entityRepository);

        try {
            $this->doctrineManager()->flush();
            return JsonResponse::create();
        } catch (DBALException $exception) {
            return JsonResponse::create('not ok', 500);
        }

    }

    /**
     * @param Booking $booking
     * @Route("/booking_confirm/{booking}", name="admin.api.booking_confirm")
     * @return JsonResponse
     */
    public function confirmBookingAjaxAction(Booking $booking) {

        $booking->setBooked(true);

        try {
            $this->doctrineManager()->flush();
            return JsonResponse::create();
        } catch (DBALException $exception) {
            return JsonResponse::create('not ok', 500);
        }

    }

    /**
     * @param File $file
     * @return JsonResponse
     * @Route("/set_image_as_default/{file}", name="admin.api.set_as_default")
     *
     */
    public function setImageAsDefaultAjaxAction(File $file) {

        $doctrine = $this->getDoctrine();

        $resp = [
            'data' => null,
            'status' => null
        ];


        $objectFiles = $doctrine->getRepository(File::class)->findBy([
            'entity' => $file->getEntity(),
            'foreignKey' => $file->getForeignKey()
        ]);


        foreach ($objectFiles as $objectFile) {
            if ($objectFile->isIsDefault() == 1) {
                $objectFile->setIsDefault(0);
            }
        }

        $file->setIsDefault(1);

        try {
            $doctrine->getManager()->flush();
            $resp['data'] = 'ok';
            $resp['status'] = 200;
        } catch (\Exception $exception) {
            $resp['data'] = 'not ok';
            $resp['status'] = 500;
        }


        return JsonResponse::create($resp['data'], $resp['status']);
    }

    /**
     * @param File $file
     * @return JsonResponse
     * @Route("/file_delete/{file}", name="admin.api.file_delete")
     */
    public function deleteFileAjaxAction(File $file) {

        $finder = new Finder();

        $fileDir = $this->getParameter('upload_directory');

        $finder->name($file->getName());

        foreach ($finder->in($fileDir) as $item) {
            unlink($item);
        }

        $this->doctrineManager()->remove($file);

        $this->doctrineManager()->flush();

        return JsonResponse::create('ok', 200);
    }

    /**
     * @param string $objectClass
     * @param int $objectId
     */
    private function deleteObjectRelatedFiles(string $objectClass, int $objectId) {

        $objectFiles = $this->doctrineManager()->getRepository(File::class)->findBy([
            'entity' => $objectClass,
            'foreignKey' => $objectId,
        ]);

        foreach ($objectFiles as $objectFile) {
            $this->deleteFileAjaxAction($objectFile);
        }

        return true;

    }
}