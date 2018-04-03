<?php


namespace AppBundle\Controller\Admin;


use AppBundle\Entity as Entity;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class HallController extends AdminController implements AdminInterface
{
    /**
     * @Route("/admin/hall/list", name="admin.hall.list")
     * @return Response
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $halls = $em->getRepository(Entity\Hall::class)->findAll();
        $categoryData = $em->getRepository(Entity\Category::class)->findOneByEntity('hall');
        $pageSeo = $this->getStaticPageSeo('hall');


        return $this->render(':default/admin/list:hall.html.twig', [
            'halls' => $halls,
            'pageSeo' => $pageSeo,
            'entity' => 'hall',
            'categoryData' => $categoryData
        ]);
    }

    /**
     * @Route("/admin/hall/create", name="admin.hall.create")
     * @param Request $request
     * @param $entity
     * @return Response
     */
    public function createAction(Request $request, $entity)
    {
        return $this->render(':default/admin/manage:hall.html.twig', []);
    }

    /**
     * @param Request $request
     * @param mixed $entity
     * @param Hall $id
     * @Route("/admin/hall/edit/{id}", name="admin.hall.edit")
     * @return Response
     */
    public function editAction(Request $request, $entity, $id)
    {
        dump($id);
        return $this->render(':default/admin/manage:hall.html.twig', []);
    }
}