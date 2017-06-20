<?php

namespace AppBundle\Controller\Front;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation as Http;

class FrontController extends Controller
{

    /**
     * @param string | null $page
     * @param int | null $id
     * @return Response
     * @Route("/",
     *     name="front.index"
     * )
     */
    public function indexAction(string $page = null, int $id = null) {

//        $env = $this->container->getParameter('kernel.environment');
//
//        if(!$page) {
//            $page = 'index';
//        }
//
//        if (!in_array($page, array_flip(self::PAGES))) {
//
//            $errorMessage = ':((9';
//
//            if ($env == 'dev') {
//                $errorMessage = 'Шаблон '.$page.'.html.twig не найден в папке app/Resources/views/default/front/page';
//            }
//
//            throw new Exception($errorMessage, 404);
//        }
//
//        if (is_int($id)) {
//            return $this->render('default/front/page/'.$page.'/default.html.twig', [
//                'defaultPage' => self::SAMPLE_PAGE,
//                'page' => $page,
//                'pages' => self::PAGES
//            ]);
//        }
//
        return $this->render(':default/front/page:index.html.twig', [
            'page' => $page,
        ]);
    }
}