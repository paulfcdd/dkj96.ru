<?php

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\Booking;
use AppBundle\Entity\Event;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\File;
use AppBundle\Entity\History;
use AppBundle\Entity\News;
use AppBundle\Entity\Review;
use AppBundle\Form\AbstractFormType;
use AppBundle\Form\NewsType;
use AppBundle\Service\FileUploaderService;
use AppBundle\Service\MailerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class AdminController extends Controller
{

    /**
     * @Route("/admin/dashboard", name="admin.index")
     */
    public function indexAction() {

        return $this->render(':default/admin:index.html.twig', [
            'bookings' => $this->getUnreadNotifications(
               Booking::class, ['booked' => 0, 'status' => 0], ['dateReceived' => 'DESC'], 10
            ),
            'messages' => $this->getUnreadNotifications(
                Feedback::class, ['status' => 0], ['dateReceived' => 'ASC'], 10
            ),
            'reviews' => $this->getUnreadNotifications(
                Review::class, ['status' => 0], ['dateReceived' => 'ASC'], 10
            )
        ]);
    }

    /**
     * @param string $className
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getUnreadNotifications(string $className, array $criteria, array $orderBy, int $limit = null, int $offset = null) {

        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository($className);

        $object = $repository->findBy($criteria, $orderBy, $limit, $offset);

        return $object;
    }

    /**
     * @param string $entity
     *
     * @return Response
     * @Route("/admin/{entity}/list", name="admin.list")
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
     * @Route("/admin/{entity}/manage/{id}", name="admin.manage")
     */
    public function manageAction(string $entity, int $id = null, Request $request) {

        $em = $this->getDoctrine()->getManager();

        $uploader = $this->get(FileUploaderService::class);

        $files = null;

        $className = ucfirst($entity);

        $class = 'AppBundle\\Entity\\'.$className;

        $object = new $class();

        if ($id) {
            $object = $em->getRepository($class)->findOneById($id);
        }

        $form = $this->entityFormBuilder($className, $object);

        if (new $object instanceof Review) {
            $form->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form
                ->getData();


            if (!new $object instanceof Review) {
                $formData->setAuthor($this->getUser());
            }

            if ($formData instanceof History) {
                $history = $em->getRepository(History::class)->findOneBy(['isEnabled' => 1]);
                if ($history) {
                    $history->setEnabled(0);
                }
            }

            if (new $object instanceof Review) {
                $formData->setStatus(1);
                $formData->setApproved(1);
            }

            $em->persist($formData);
            $em->flush();

            if (isset($form['files'])) {
                $attachedFiles = $form['files']->getData();

                if (!empty($attachedFiles)) {

                    foreach ($attachedFiles as $attachedFile) {

                        $file = new File();

                        $uploader
                            ->setDir($entity)
                            ->setFile($attachedFile);

                        $file
                            ->setForeignKey($formData->getId())
                            ->setMimeType($uploader->getMimeType())
                            ->setEntity($class)
                            ->setName($uploader->upload());

                        $em->persist($file);

                    }

                    $em->flush();
                }
            }

            return $this->redirectToRoute('admin.manage', [
                'entity' => $entity,
                'id' => $formData->getId()
            ]);
        }

        return $this->render(':default/admin:manage.html.twig', [
            'form' => $form->createView(),
            'object' => $object,
        ]);
    }

    /**
     * @Route("/admin/{entity}/manage/{id}/files", name="admin.manage.files")
     */
    public function fileManagerAction(string $entity, int $id) {

        return $this->render(':default/admin:files.html.twig', [
            'files' => $this->fileLoader($this->getClassName($entity), $id),
        ]);
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


    /**
     * @param string $class
     * @param int $id
     * @return array
     */
    private function fileLoader(string $class, int $id) {

        $doctrine = $this->getDoctrine();

        $files = $doctrine->getRepository(File::class)->findBy(
            ['foreignKey' => $id, 'entity' => $class]
        );

        return $files;

    }

    protected function getClassName(string $entity) {

        $className = ucfirst($entity);

        $class = 'AppBundle\\Entity\\'.$className;

        return $class;
    }

    /**
     * @param string $entity
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getEntityRepository(string $entity) {

        return $this->getDoctrine()->getRepository($this->getClassName($entity));

    }

}