<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\File;
use Doctrine\DBAL\DBALException;
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
     * @param Booking $booking
     * @return JsonResponse
     * @Route("/mark_as_unread/{booking}", name="admin.api.booking_unread")
     */
    public function markAsUnreadAjaxAction(Booking $booking) {

        $booking->setStatus(0);

        $this->doctrineManager()->persist($booking);

        try {
            $this->doctrineManager()->flush();
            return JsonResponse::create('ok');
        } catch (DBALException $exception) {
            return JsonResponse::create('not ok', 500);
        }

    }

    /**
     * @param Booking $booking
     * @return JsonResponse
     * @Route("/booking_delete/{booking}", name="admin.api.booking_delete")
     */
    public function deleteBookingAjaxAction(Booking $booking) {

        $this->doctrineManager()->remove($booking);

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
}