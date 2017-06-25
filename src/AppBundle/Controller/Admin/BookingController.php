<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Booking;
use AppBundle\Entity\News;
use AppBundle\Form\AbstractFormType;
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

/**
 * Class BookingController
 * @package AppBundle\Controller\Admin
 * @Route("/admin/bookings")
 */
class BookingController extends AdminController
{
    /**
     * @return Response
     * @Route("", name="admin.booking.listing")
     */
    public function listBookingsAction() {

        $doctrine = $this->getDoctrine();

        return $this->render(':default/admin/booking:list.html.twig',[
            'bookings' => $doctrine->getRepository(Booking::class)
                ->findBy([], ['dateReceived' => 'DESC'], null, null),
        ]);
    }

    /**
     * @Route("/detail/{booking}", name="admin.booking.details")
     */
    public function bookingDetailAction(Booking $booking, Request $request) {

        $doctrine = $this->getDoctrine();

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
     * @Route("/compose/{booking}", name="admin.booking.compose")
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

}