<?php

namespace AppBundle\Controller\Front;


use AppBundle\Entity\Artist;
use AppBundle\Entity\Booking;
use AppBundle\Entity\Event;
use AppBundle\Entity\Hall;
use AppBundle\Entity\History;
use AppBundle\Entity\News;
use AppBundle\Form\BookingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation as Http;

class FrontController extends Controller
{

    /**
     * @param string | null $page
     * @param int | null $id
     * @return Http\Response
     * @Route("/",
     *     name="front.index"
     * )
     */
    public function indexAction(string $page = null, int $id = null) {

        $doctrine = $this->getDoctrine();

        return $this->render(':default/front/page:index.html.twig', [
            'page' => $page,
            'news' => $doctrine->getRepository(News::class)->findAll(),
            'events' => $doctrine->getRepository(Event::class)->findBy([], ['eventDate' => 'ASC'], 3, null)
        ]);
    }

    /**
     * @Route("/artists", name="front.artists")
     */
    public function listArtistsPage() {
        return $this->render(':default/front/page:artisty.html.twig', [
            'artists' => $this->getDoctrine()->getRepository(Artist::class)->findAll(),
        ]);
    }

    /**
     * @Route("/history", name="front.history")
     */
    public function listHistoryPage() {
        return $this->render(':default/front/page:istoriya.html.twig', [
            'history' => $this->getDoctrine()->getRepository(History::class)->findAll(),
        ]);
    }

    /**
     * @Route("/contact", name="front.contact")
     */
    public function contactPageAction() {
        return $this->render(':default/front/page:kontakty.html.twig', []);
    }


    /**
     * @param $event
     * @param $request
     * @return Http\Response
     * @Route(
     *     "/event/details/id{event}",
     *     name="event.details_page"
     * )
     */
    public function eventDetailAction(Event $event, Http\Request $request) {

        return $this->render(':default/front/page/event:details.html.twig', [
            'event' => $event
        ]);

    }

    /**
     * @Route("/halls/list", name="halls.list")
     */
    public function listHallsAction() {

        $doctrine = $this->getDoctrine();

        return $this->render(':default/front/page:halls.html.twig', [
            'halls' => $doctrine->getRepository(Hall::class)->findAll(),
        ]);
    }

    /**
     * @Route("/halls/info/hall{hall}", name="halls.detail")
     */
    public function hallInfoAction(Hall $hall) {

        return $this->render('default/front/page/halls/default.html.twig', [
            'hall' => $hall
        ]);
    }
}