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
 * @Route("/admin")
 */
class AdminController extends Controller
{

    /**
     * @Route("", name="admin.index")
     */
    public function indexAction() {

        return $this->render(':default/admin:index.html.twig');
    }

    /**
     * @param string $entity
     *
     * @return Response
     * @Route("/{entity}/list", name="admin.list")
     */
    public function listAction(string $entity) {

        $em = $this->getDoctrine()->getManager();

        $class = ucfirst($entity);

        $repository = $em->getRepository('AppBundle\\Entity\\'.$class);

        return $this->render(':default/admin:list.html.twig', [
            'objects' => $repository->findAll(),
        ]);
    }

    /**
     * @param $entity
     * @param $id
     * @return Response
     * @Route("/{entity}/manage/{id}", name="admin.manage")
     */
    public function manageAction(string $entity, int $id = null, Request $request) {

        $em = $this->getDoctrine()->getManager();

        $className = ucfirst($entity);

        $class = 'AppBundle\\Entity\\'.$className;

        $object = new $class();

        if ($id) {
            $object = $em->getRepository('AppBundle\\Entity\\'.$className)->findOneById($id);
        }

        $form = $this
            ->entityFormBuilder($className, $object)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form
                ->getData()
                ->setAuthor($this->getUser());

            $em->persist($formData);
            $em->flush();

            return $this->redirectToRoute('admin.manage', [
                'entity' => $entity,
                'id' => $formData->getId()
            ]);
        }

        return $this->render(':default/admin:manage.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @return Response
     * @Route("/bookings", name="admin.booking.listing")
     */
    public function listBookingsAction() {

        $doctrine = $this->getDoctrine();

        return $this->render(':default/admin/booking:list.html.twig',[
            'bookings' => $doctrine->getRepository(Booking::class)->findBy([], ['dateReceived' => 'DESC'], null, null)
        ]);
    }

    /**
     * @Route("/bookings/detail/{booking}", name="admin.booking.details")
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
     * @Method({"POST", "GET"})
     * @Route("/bookings/compose/{booking}", name="admin.booking.compose")
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
     * @param Booking $booking
     * @return bool
     */
    private function changeBookingStatus(Booking $booking) {
        $booking->setStatus(1);

        $this->getDoctrine()->getManager()->flush();

        return true;
    }

    /**
     * @param $className
     * @param $object
     * @return Form
     */
    private function entityFormBuilder($className, $object) {

        $formName = 'AppBundle\Form\\'.$className.'Type';

        $form = $this->createForm($formName, $object);

        return $form;

    }

    private function saveFormData($formData) {

        $em = $this->getDoctrine()->getManager();

    }


}