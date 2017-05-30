<?php

namespace AppBundle\Controller\Front;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends Controller
{

    /**
     * @Route("/", name="app.index")
     */
    public function indexAction() {
        return $this->render(':default/front:index.html.twig');
    }
}