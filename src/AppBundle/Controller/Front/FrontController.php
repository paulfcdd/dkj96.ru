<?php

namespace AppBundle\Controller\Front;


use AppBundle\Entity\Booking;
use AppBundle\Entity\Event;
use AppBundle\Entity\Hall;
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

    /**
     * @Route("/halls/booking/hall{hall}", name="halls.book_hall")
     */
    public function bookHallAction(Hall $hall, Http\Request $request) {

        /** @var Http\Session\Session $session */
        $session = $request->getSession();

        /** @var Http\Session\Flash\FlashBag $flashBag */
        $flashBag = $session->getFlashBag();

        $flashBagMessage = null;

        $doctrine = $this->getDoctrine();

        $form = $this
            ->createForm(BookingType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $flashBag->add('success', 'Ваш запрос отправлен');
            $flashBag->add('error', 'Не удалось отправить форму');

            /** @var Booking $formData */
            $formData = $form->getData();

            $formData->setHall($hall);
            $doctrine->getManager()->persist($formData);

            try {
                $doctrine->getManager()->flush();
                $flashBagMessage = $flashBag->get('success');
            } catch (\Exception $exception) {
                $flashBagMessage = $flashBag->get('error');
            }
        }

        return $this->render(':default/front/page:booking.html.twig', [
            'hall' => $hall,
            'form' => $form->createView(),
            'formMessage' => $flashBagMessage,
        ]);
    }
}