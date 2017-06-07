<?php

namespace AppBundle\Controller\Front;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation as Http;

class FrontController extends Controller
{

    const ARTISTS = [
        0 => [
            'name' => 'Шоу барабанщиков',
            'description' => '',
            'avatar' => 'http://dss-sverdl.ru.images.1c-bitrix-cdn.ru/upload/iblock/84e/84e76b7e2f48654277560d2f8332745a.jpg'
        ],
        1 => [
            'name' => 'Шоу-группа "ЭЙФОРИЯ"',
            'description' => '',
            'avatar' => 'http://dss-sverdl.ru.images.1c-bitrix-cdn.ru/upload/iblock/84e/84e76b7e2f48654277560d2f8332745a.jpg'
        ],
        2 => [
            'name' => 'Танцевальный театр "ФОРА"',
            'description' => '',
            'avatar' => 'http://dss-sverdl.ru.images.1c-bitrix-cdn.ru/upload/iblock/84e/84e76b7e2f48654277560d2f8332745a.jpg'
        ],
        3 => [
            'name' => 'Танцевальный центр "АИСТ"',
            'description' => '',
            'avatar' => 'http://dss-sverdl.ru.images.1c-bitrix-cdn.ru/upload/iblock/84e/84e76b7e2f48654277560d2f8332745a.jpg'
        ]
        , 4 => [
            'name' => 'Театр танца "СТЕП"',
            'description' => '',
            'avatar' => 'http://dss-sverdl.ru.images.1c-bitrix-cdn.ru/upload/iblock/84e/84e76b7e2f48654277560d2f8332745a.jpg'
        ],
        5 => [
            'name' => 'Шоу-группа эстрадного вокала "Мелодика"',
            'description' => '',
            'avatar' => 'http://dss-sverdl.ru.images.1c-bitrix-cdn.ru/upload/iblock/84e/84e76b7e2f48654277560d2f8332745a.jpg'
        ],
    ];

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
            'artists' => self::ARTISTS,
        ]);
    }
}