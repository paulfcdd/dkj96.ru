<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\News;
use AppBundle\Form\AbstractFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
            ->createForm(AbstractFormType::class, $object)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData()->setAuthor($this->getUser());
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

}