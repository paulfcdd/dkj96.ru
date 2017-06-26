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
     * @return Response
     * @Route("/admin/bookings", name="admin.booking.listing")
     */
    public function listBookingsAction() {

        $doctrine = $this->getDoctrine();

        return $this->render(':default/admin/booking:list.html.twig',[
            'bookings' => $doctrine->getRepository(Booking::class)
                ->findBy([], ['dateReceived' => 'DESC'], null, null),
        ]);
    }

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
     * @Route("/admin/bookings/compose/{booking}", name="admin.booking.compose")
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
     * @Route("/admin/bookings/calendar/{hall}", name="admin.booking.calendar")
     */
    public function bookingCalendarAction(Hall $hall, Request $request) {

        $bookings = $this->getDoctrine()->getRepository(Booking::class)->findBy(['hall'=>$hall]);

        return $this->render(':default/admin/booking:calendar.html.twig', [
            'hall' => $hall,
            'bookings' => $bookings
        ]);
    }

    /**
     * @Route("/admin/bookings/halls/booking/hall{hall}", name="halls.book_hall")
     */
    public function bookHallAction(Hall $hall, Request $request) {

        /** @var Session $session */
        $session = $request->getSession();

        /** @var FlashBag $flashBag */
        $flashBag = $session->getFlashBag();

        $flashBagMessage = null;

        $doctrine = $this->getDoctrine();

        $form = $this
            ->createForm(BookingType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $flashBag->add('success', 'Ваш запрос отправлен');
            $flashBag->add('error', 'Не удалось отправить форму');

            /** @var Booking $formData */
            $formData = $form->getData();

            $formData->setHall($hall);
            $doctrine->getManager()->persist($formData);

            try {
                $doctrine->getManager()->flush();
                $flashBagMessage = $flashBag->get('success');
            } catch (\Exception $exception) {
                $flashBagMessage = $flashBag->get('error');
            }
        }

        return $this->render(':default/front/page:booking.html.twig', [
            'hall' => $hall,
            'form' => $form->createView(),
            'formMessage' => $flashBagMessage,
        ]);
    }

}