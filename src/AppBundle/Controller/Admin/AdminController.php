<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin.index")
     */
    public function indexAction() {

        return $this->render(':default/admin:index.html.twig');
    }
}