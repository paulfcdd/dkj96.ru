<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Booking;
use AppBundle\Entity\Hall;
use AppBundle\Entity\News;
use AppBundle\Form\AbstractFormType;
use AppBundle\Form\BookingType;
use AppBundle\Form\NewsType;
use AppBundle\Service\MailerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class BookingController
 * @package AppBundle\Controller\Admin
 */
class BookingController extends AdminController
{

    /**
     * @Route("/admin/bookings/detail/{booking}", name="admin.booking.details")
     */
    public function bookingDetailAction(Booking $booking, Request $request) {


        if (!$booking->isStatus()) {
            $this->changeBookingStatus($booking);
        }


        return $this->render('default/admin/booking/detail.html.twig', [
            'booking' => $booking
        ]);
    }

    /**
     * @param Booking $booking
     * @return bool
     */
    private function changeBookingStatus(Booking $booking) {
        $booking->setStatus(1);

        $this->getDoctrine()->getManager()->flush();

        return true;
    }

    /**
     * @Method({"POST", "GET"})
     * @Route("/admin/booking/compose/{booking}", name="admin.booking.compose")
     */
    public function bookingComposeAction(Booking $booking, Request $request) {

        $mailer = $this->get(MailerService::class);

        if ($request->isMethod('POST')) {
            $emailForm = $request->request->all();

            $mailer
                ->setSubject($emailForm['email-subject'])
                ->setFrom($this->getParameter('mail_from'))
                ->setTo($emailForm['email-to'])
                ->setBody($emailForm['email-body']);

            $mailer->sendMessage();
        }

        return $this->render(':default/admin/booking:compose.html.twig', [
            'booking' => $booking,
        ]);
    }

    public function renderBookingMenuAction() {

        return $this->render(':default/admin/booking:sidebar.html.twig', [
            'bookings' => $this->getDoctrine()->getRepository(Booking::class)->findAll()
        ]);
    }

    /**
     * @param Hall $hall
     * @param Request $request
     * @return Response
     * @Route("/admin/booking/calendar/{hall}", name="admin.booking.calendar")
     */
    public function bookingCalendarAction(Hall $hall, Request $request) {

        $bookings = $this->getDoctrine()->getRepository(Booking::class)->findBy(
            [
                'hall'=>$hall,
                'booked' => true,
            ]);

        return $this->render(':default/admin/booking:calendar.html.twig', [
            'hall' => $hall,
            'bookings' => $bookings
        ]);
    }
}