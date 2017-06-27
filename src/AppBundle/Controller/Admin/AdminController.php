<?php

namespace AppBundle\Controller\Admin;


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

        return $this->render(':default/admin:index.html.twig');
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
            $object = $em->getRepository('AppBundle\\Entity\\'.$className)->findOneById($id);
            $files = $this->fileLoader($class, $id);
        }

        $form = $this->entityFormBuilder($className, $object);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form
                ->getData()
                ->setAuthor($this->getUser());

            if ($formData instanceof History) {
                $history = $em->getRepository(History::class)->findOneBy(['isEnabled' => 1]);
                $history->setEnabled(0);
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
            'files' => $files,
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

    private function getClassName(string $entity) {

        $className = ucfirst($entity);

        $class = 'AppBundle\\Entity\\'.$className;

        return $class;
    }


}