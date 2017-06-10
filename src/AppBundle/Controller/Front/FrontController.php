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

    const SAMPLE_PAGE = [
        'id' => 0,
        'title' => 'Концертный зал',
        'capacity' => 'до 50 человек',
        'description' => '
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ut erat auctor odio finibus facilisis eu et ligula. Fusce bibendum gravida lacus. Proin ac sodales purus. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Suspendisse placerat porta odio, eu vehicula lacus gravida ut. Quisque condimentum ipsum in leo malesuada, vel elementum tortor viverra. Nulla justo quam, convallis a venenatis vel, tempus ac augue. Donec placerat ligula convallis, venenatis dui in, mattis nisi. Curabitur eu vestibulum lacus. Curabitur sollicitudin odio in egestas viverra. Etiam gravida maximus sapien, sed consequat justo auctor eu. Phasellus quis justo vulputate, luctus turpis ut, ultricies nulla.</p>
        ',
    ];


    /**
     * @param string | null $page
     * @param int | null $id
     * @return Response
     * @Route("/{page}/{id}",
     *     name="front.renderPage"
     * )
     */
    public function pageRenderAction(string $page = null, int $id = null) {

        $env = $this->container->getParameter('kernel.environment');

        if(!$page) {
            $page = 'index';
        }

        if (!in_array($page, array_flip(self::PAGES))) {

            $errorMessage = ':((9';

            if ($env == 'dev') {
                $errorMessage = 'Шаблон '.$page.'.html.twig не найден в папке app/Resources/views/default/front/page';
            }

            throw new Exception($errorMessage, 404);
        }


//            switch ($id){
//                case 0:
//                    return $this->render('default/front/page/'.$page.'/default.html.twig', [
//                        'defaultPage' => self::SAMPLE_PAGE,
//                        'page' => $page,
//                        'pages' => self::PAGES
//                    ]);
//                    break;
//                default:
//                    $id = 0;
//                    return $this->redirectToRoute('front.renderPage', [
//                        'page' => $page,
//                        'id' => $id
//                    ]);
//            }


        return $this->render(':default/front/page:'.$page.'.html.twig', [
            'page' => $page,
            'pageData' => self::PAGES,
            'artists' => self::ARTISTS,
        ]);
    }
}