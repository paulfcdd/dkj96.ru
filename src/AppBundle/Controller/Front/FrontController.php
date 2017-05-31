<?php

namespace AppBundle\Controller\Front;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends Controller
{

    /**
     * @param string $page
     * @return Response
     * @Route("/{page}", name="front.renderMenu")
     */
    public function mainMenuPageRenderAction(string $page = null) {

        if(!$page) {
            $page = 'index';
        }

        return $this->render(':default/front/page:'.$page.'.html.twig');
    }
}