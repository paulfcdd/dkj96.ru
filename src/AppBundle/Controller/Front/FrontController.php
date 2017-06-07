<?php

namespace AppBundle\Controller\Front;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation as Http;

class FrontController extends Controller
{


    const PAGES = [
        'index' => 'Главная',
        'zaly' => 'Залы',
        'artisty' => 'Артисты',
        'istoriya' => 'История',
        'otzyvy' => 'Отзывы',
        'kontakty' => 'Контакты',
        'booking' => 'Бронирование'
    ];


    /**
     * @param string $page
     * @param Http\Request $request
     * @return Response
     * @Route("/{page}",
     *     name="front.renderPage"
     * )
     */
    public function mainMenuPageRenderAction(string $page = null, Http\Request $request) {

        $response = new Http\Response();

        $env = $this->container->getParameter('kernel.environment');

        if(!$page) {
            $page = 'index';
        }

        if (!in_array($page, array_flip(self::PAGES))) {

            $errorMessage = '';

            if ($env == 'dev') {
                $errorMessage = 'Шаблон '.$page.'.html.twig не найден в папке app/Resources/views/default/front/page';
            }

            throw new Exception($errorMessage, 404);
        }

        return $this->render(':default/front/page:'.$page.'.html.twig', [
            'page' => $page,
            'pageData' => self::PAGES,
        ]);
    }
}