<?php

namespace AppBundle\Controller\Front;


use AppBundle\Entity\Artist;
use AppBundle\Entity\Booking;
use AppBundle\Entity\Event;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Hall;
use AppBundle\Entity\History;
use AppBundle\Entity\News;
use AppBundle\Entity\Review;
use AppBundle\Form\BookingType;
use AppBundle\Form\FeedbackType;
use AppBundle\Form\ReviewType;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Tests\Compiler\H;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation as Http;

class FrontController extends Controller
{

    /**
     * @param string | null $page
     * @return Http\Response
     * @Route("/",
     *     name="front.index"
     * )
     */
    public function indexAction(string $page = null) {

        return $this->render(':default/front/page:index.html.twig', [
            'page' => $page,
            'events' => $this->getSortedList(
                Event::class,['eventDate' => 'ASC'], new \DateTime(), 'eventDate', 3
            ),
            'reviews' => $this->getSortedList(
                Review::class, ['dateReceived' => 'DESC'], new \DateTime(), null, 2
            )
        ]);
    }

    /**
     * @param News $news
     * @return Http\Response
     * @Route("/news/{news}", name="front.news.single")
     */
    public function singleNewsPageAction(News $news) {

        return $this->render(':default/front/page/news:single.html.twig', [
            'news' => $news
        ]);
    }

    /**
     * @return Http\Response
     * @Route("/portfolio", name="front.portfolio")
     */
    public function portfolioAction() {

        /** @var EntityManager $eventRepo */
        $eventRepo = $this->getDoctrine()->getRepository(Event::class);

        /** @var EntityManager $newsRepo */
        $newsRepo = $this->getDoctrine()->getRepository(News::class);

        $eventQB = $eventRepo->createQueryBuilder('e')
            ->where('e.eventDate > :filterdate')
            ->setParameter('filterdate', new \DateTime())
            ->setMaxResults(6)
            ->orderBy('e.eventDate', 'ASC')
            ->getQuery();

        $newsQB = $newsRepo->createQueryBuilder('n')
            ->where(':filterdate BETWEEN n.publishStartDate AND n.publishEndDate')
            ->setParameter('filterdate', new \DateTime())
            ->setMaxResults(6)
            ->orderBy('n.dateCreated', 'DESC')
            ->getQuery();






        return $this->render(':default/front/page:portfolio.html.twig', [
            'events' => $eventQB->getResult(),
            'news' => $newsQB->getResult(),
        ]);
    }

    /**
     * @Route("/artists", name="front.artists")
     */
    public function listArtistsPageAction() {
        return $this->render(':default/front/page:artisty.html.twig', [
            'artists' => $this->getDoctrine()->getRepository(Artist::class)->findAll(),
        ]);
    }

    /**
     * @param $artist
     * @return Http\Response
     * @Route("/artists/detail/{artist}", name="front.artists.detail")
     */
    public function singleArtistAction(Artist $artist) {

        return $this->render(':default/front/page/artists:single.html.twig', [
            'artist' => $artist,
        ]);
    }

    /**
     * @Route("/history", name="front.history")
     */
    public function listHistoryPageActio() {

        return $this->render(':default/front/page:istoriya.html.twig', [
            'history' => $this->getDoctrine()->getRepository(History::class)->findOneBy(['isEnabled' => true]),
        ]);
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     * @Route("/contact", name="front.contact")
     */
    public function contactPageAction(Http\Request $request) {

        $em = $this->getDoctrine()->getManager();

        $form = $this
            ->createForm(FeedbackType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($form->getData());
            try {
                $em->flush();
                $this->addFlash('success', 'Ваше сообщение успешно выслано.');
            } catch (DBALException $exception) {
                $this->addFlash('error', 'Не удалось выслать сообщение, попробуйте позже');
            }
        }

        return $this->render(':default/front/page:kontakty.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/event/list", name="event.list")
     */
    public function eventListAction() {

        $em = $this->getDoctrine()->getManager();

        return $this->render(':default/front/page/event:list.html.twig', [
            'events' => $em->getRepository(Event::class)->findAll(),
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
     * @Method({"POST", "GET"})
     */
    public function eventDetailAction(Event $event, Http\Request $request) {

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ReviewType::class)->handleRequest($request);

        if ($request->isMethod('POST')) {

            if ($form->isSubmitted() && $form->isValid()) {

                $formData = $form->getData();

                $formData->setEvent($event);

                $em->persist($formData);

                $em->flush();
            }

        }

        return $this->render(':default/front/page/event:details.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
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
     * @param Hall $hall
     * @return Http\Response
     * @Route("/halls/info/hall/{hall}", name="halls.detail")
     */
    public function hallInfoAction(Hall $hall) {

        $em = $this->getDoctrine()->getManager();

        $bookings = $em->getRepository(Booking::class)->findBy([
            'hall' => $hall,
            'booked' => true,
            ]);

        return $this->render('default/front/page/halls/default.html.twig', [
            'hall' => $hall,
            'bookings' => $bookings,
        ]);
    }

    /**
     * @param Hall|null $hall
     * @param Http\Request $request
     * @return Http\Response
     * @Route("/halls/booking/{hall}", name="halls.book_hall")
     */
    public function bookHallAction(Hall $hall = null, Http\Request $request) {

        /** @var Http\Session\Session $session */
        $session = $request->getSession();

        /** @var Http\Session\Flash\FlashBag $flashBag */
        $flashBag = $session->getFlashBag();

        $flashBagMessage = null;

        $doctrine = $this->getDoctrine();

        $form = $this->createForm(BookingType::class);

        if (!$hall) {
            $form->add('hall', EntityType::class, [
                'class' => Hall::class,
                'label' => 'Выберите зал',
                'attr' => [
                    'class' => 'form-control no-border-radius'
                ],
                'choice_label' => 'title',
                'required' => false,
                'placeholder' => null
            ]);
        }


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $flashBag->add('success', 'Ваш запрос отправлен');
            $flashBag->add('error', 'Не удалось отправить форму');

            /** @var Booking $formData */
            $formData = $form->getData();

            if ($hall) {
                $formData->setHall($hall);
            }

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
            'halls' => $doctrine->getRepository(Hall::class)->findAll(),
        ]);
    }

    /**
     * @param Hall $hall
     * @param Http\Request $request
     * @return Http\Response
     * @Route("/halls/{hall}/booking-calendar", name="halls.booking_calendar")
     * @Method({"POST"})
     */
    public function renderBookingsModalAction(Hall $hall, Http\Request $request) {

        return $this->render(':default/front/page/halls:hall_calendar_modal.html.twig', [
            'bookings' => $hall->getBookings()

        ]);
    }

    /**
     * @param string $class
     * @param array $orderBy
     * @param \DateTime $filterDate
     * @param string $selectField
     * @param int|null $limit
     * @return array
     */
    private function getSortedList(string $class, array $orderBy, \DateTime $filterDate = null, string $selectField = null, int $limit = null) {

        /** @var EntityManager $repository */
        $repository = $this->getDoctrine()->getRepository($class);

        $qb = $repository->createQueryBuilder('a');


        if ($selectField && $filterDate) {
            $qb->where('a.' . $selectField . ' > :filterdate')
                ->setParameter('filterdate', $filterDate);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }


        $qb =$qb
            ->orderBy('a.'.key($orderBy), current($orderBy))
            ->getQuery();

        return $qb->getResult();
    }
}