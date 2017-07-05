<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Feedback;
use AppBundle\Entity\Booking;
use AppBundle\Entity\Review;
use AppBundle\Service\MailerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends AdminController
{
    const MESSAGES_ENTITY_NAMES_MAP = [
        'booking' => 'Бронирование',
        'feedback' => 'Обр. связь',
        'review' => 'Отзывы'
    ];

    /**
     * @param $entity
     * @return Response
     * @Route("/admin/messages/{entity}", name="admin.message.listing")
     */
    public function renderMessagesList($entity) {

        $em = $this->getDoctrine()->getManager();

        $addReviewButton = false;

        $entityClass = $this->getClassName($entity);

        if (new $entityClass instanceof Review) {
            $addReviewButton = true;
        }

        return $this->render(':default/admin/messages:list.html.twig',[
            'objects' => $em->getRepository($entityClass)
                ->findBy([], ['dateReceived' => 'DESC'], null, null),
            'theme' => self::MESSAGES_ENTITY_NAMES_MAP[$entity],
            'entity' => $entity,
            'addReviewButton' => $addReviewButton,

        ]);

    }

    /**
     * @param $entity
     * @return Response
     */
    public function renderMessageMenuAction($entity) {

        $entityClass = $this->getClassName($entity);

        return $this->render(':default/admin/messages:sidebar.html.twig', [
            'entity' => $entity,
            'objects' => $this->getDoctrine()->getRepository($entityClass)->findAll()
        ]);
    }

    /**
     * @param $entity
     * @param $id
     * @return Response
     * @Route("/admin/messages/detail/{entity}/{id}", name="admin.messages.details")
     */
    public function messageDetailAction(string $entity, $id) {

        $msgSubject = null;

        $bookHallButton = false;

        $approveButton = false;


        $entityRepository = $this->getEntityRepository($entity)->findOneById($id);

        if ($entityRepository instanceof Feedback) {
            $msgSubject = 'Обратная связь';
        }

        if ($entityRepository instanceof Booking) {
            $msgSubject = 'Запрос на бронирование';
            $bookHallButton = true;
        }

        if ($entityRepository instanceof Review) {
            $msgSubject = 'Отзыв о мероприятии '.$entityRepository->getEvent()->getTitle();
            $approveButton = true;
        }

        if ($entityRepository && !$entityRepository->isStatus()) {
            $entityRepository->setStatus(1);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render(':default/admin/messages:detail.html.twig', [
            'object' => $entityRepository,
            'msgSubject' => $msgSubject,
            'enableBookBtn' => $bookHallButton,
            'approveButton' => $approveButton,
        ]);
    }

    /**
     * @Method({"POST", "GET"})
     * @Route("/admin/message/compose/{entity}/{id}", name="admin.message.compose")
     */
    public function messageComposeAction($entity, $id, Request $request) {

        if ($request->isMethod('POST')) {

            $mailer = $this->get(MailerService::class);

            $emailForm = $request->request->all();

            $mailer
                ->setSubject($emailForm['email-subject'])
                ->setFrom($this->getParameter('mail_from'))
                ->setTo($emailForm['email-to'])
                ->setBody($emailForm['email-body']);

            $mailer->sendMessage();
        }

        $entityRepository = $this->getEntityRepository($entity)->findOneById($id);

        return $this->render(':default/admin/messages:compose.html.twig', [
            'object' => $entityRepository,
        ]);
    }

    public function renderBookingMenuAction() {

        return $this->render(':default/admin/booking:sidebar.html.twig', [
            'bookings' => $this->getDoctrine()->getRepository(Booking::class)->findAll()
        ]);
    }
}