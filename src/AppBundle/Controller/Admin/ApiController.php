<?php

namespace AppBundle\Controller\Admin;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Booking;
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
}