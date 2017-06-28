<?php

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\Feedback;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Booking;
use AppBundle\Entity\File;
use AppBundle\Entity\History;
use AppBundle\Entity\News;
use AppBundle\Form\AbstractFormType;
use AppBundle\Form\NewsType;
use AppBundle\Service\FileUploaderService;
use AppBundle\Service\MailerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends AdminController
{
    const MESSAGES_ENTITY_NAMES_MAP = [
        'booking' => 'Бронирование',
        'feedback' => 'Обр. связь',
    ];

    /**
     * @return Response
     * @Route("/admin/messages/{entity}", name="admin.message.listing")
     */
    public function renderMessagesList($entity) {

        $em = $this->getDoctrine()->getManager();

        $entityClass = $this->getClassName($entity);

        return $this->render(':default/admin/messages:list.html.twig',[
            'objects' => $em->getRepository($entityClass)
                ->findBy([], ['dateReceived' => 'DESC'], null, null),
            'theme' => self::MESSAGES_ENTITY_NAMES_MAP[$entity],
            'entity' => $entity,
        ]);

    }

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

        $enableBookBtn = false;


        $entityRepository = $this->getEntityRepository($entity)->findOneById($id);

        if ($entityRepository instanceof Feedback) {
            $msgSubject = 'Обратная связь';
        }

        if ($entityRepository instanceof Booking) {
            $msgSubject = 'Запрос на бронирование';
            $enableBookBtn = true;
        }

        if (!$entityRepository->isStatus()) {
            $entityRepository->setStatus(1);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render(':default/admin/messages:detail.html.twig', [
            'object' => $entityRepository,
            'msgSubject' => $msgSubject,
            'enableBookBtn' => $enableBookBtn,
        ]);
    }
}