<?php

namespace AppBundle\Controller\Front;


use AppBundle\Entity\Artist;
use AppBundle\Entity\Booking;
use AppBundle\Entity\Event;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Hall;
use AppBundle\Entity\History;
use AppBundle\Entity\News;
use AppBundle\Entity\Portfolio;
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
use Symfony\Component\Form\Form;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation as Http;
use AppBundle\Service\FileUploaderService;

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

        /** @var EntityManager $eventRepo */
        $eventRepo = $this->getDoctrine()->getRepository(Event::class);

        /** @var EntityManager $newsRepo */
        $newsRepo = $this->getDoctrine()->getRepository(News::class);

        $eventQB = $eventRepo->createQueryBuilder('e')
            ->where('e.eventDate > :filterdate')
            ->setParameter('filterdate', new \DateTime())
            ->setMaxResults(6)
            ->orderBy('e.eventDate', 'DESC')
            ->getQuery();

        $newsQB = $newsRepo->createQueryBuilder('n')
            ->where(':filterdate BETWEEN n.publishStartDate AND n.publishEndDate')
            ->setParameter('filterdate', new \DateTime())
            ->setMaxResults(6)
            ->orderBy('n.dateCreated', 'DESC')
            ->getQuery();

        return $this->render(':default/front/page:index.html.twig', [
            'page' => $page,
            'events' => $eventQB->getResult(),
            'news' => $newsQB->getResult(),
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
     * @Route("/reviews/list", name="front.review_list")
     */
    public function reviewListAction() {

        $em = $this->getDoctrine()->getManager();

        return $this->render(':default/front/page/review:list.html.twig', [
            'reviews' => $em->getRepository(Review::class)->findBy(['approved' => 1, 'status' => 1]),
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


        if ($form->isSubmitted()) {

            $response = $request->request->get('g-recaptcha-response');

            $resaptchaVerifyer = $this->googleRecaptchaVerifyer($response);

            $resaptchaVerifyer = json_decode($resaptchaVerifyer);

            if ($form->isValid() && $resaptchaVerifyer->success) {
                $em->persist($form->getData());

                try {
                    $em->flush();
                    $this->addFlash('success', 'Ваше сообщение успешно выслано.');
                } catch (DBALException $exception) {
                    $this->addFlash('error', 'Не удалось выслать сообщение, попробуйте позже');
                }
            } else {
                $this->addFlash('error', 'Вы должны подтвердить, что вы не робот');
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

        $showBuyTicketBtn = false;

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ReviewType::class)->handleRequest($request);

        if ($event->getEventDate() > new \DateTime()) {
            $showBuyTicketBtn = true;
        }
        if ($form->isSubmitted()) {
            $response = $request->request->get('g-recaptcha-response');

            $resaptchaVerifyer = $this->googleRecaptchaVerifyer($response);

            $resaptchaVerifyer = json_decode($resaptchaVerifyer);

            if ($form->isValid() && $resaptchaVerifyer->success) {

                $formData = $form->getData();

                $formData->setEvent($event);

                $em->persist($formData);

                $em->flush();

                $this->addFlash('success', 'Отзыв отправлен');

            } else {
                $this->addFlash('error', 'Вы должны подтвердить, что вы не робот');
            }
        }

        return $this->render(':default/front/page/event:details.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'showButton' => $showBuyTicketBtn,
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
            'imagesExt' => FileUploaderService::IMAGES,
            'videosExt' => FileUploaderService::VIDEOS,
        ]);
    }

    /**
     * @param Hall|null $hall
     * @param Http\Request $request
     * @return Http\Response
     * @Route("/halls/booking/{hall}", name="halls.book_hall")
     */
    public function bookHallAction(Hall $hall = null, Http\Request $request) {

        $doctrine = $this->getDoctrine();

        $form = $this->createForm(BookingType::class);

        if (!$hall) {
            $form->add('hall', EntityType::class, [
                'class' => Hall::class,
                'label' => 'Выберите зал',
                'attr' => [
                    'class' => 'form-control no-border-radius'
                ],
                'choice_label' => function ($hall) {
                return $hall->getTitle().' - '.$hall->getCapacity().' чел.';
                },
                'required' => false,
                'placeholder' => null
            ]);
        }


        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $response = $request->request->get('g-recaptcha-response');

            $resaptchaVerifyer = $this->googleRecaptchaVerifyer($response);

            $resaptchaVerifyer = json_decode($resaptchaVerifyer);

            if ($form->isValid() && $resaptchaVerifyer->success) {
                /** @var Booking $formData */
                $formData = $form->getData();

                if ($hall) {
                    $formData->setHall($hall);
                }

                $doctrine->getManager()->persist($formData);

                try {
                    $doctrine->getManager()->flush();
                    $this->addFlash('success', 'Заявка на бронь отправлена');
                } catch (\Exception $exception) {
                    $this->addFlash('error', 'Во время отправления заявки произошла ошибка, попробуйте позже');
                }
            } else {
                $this->addFlash('error', 'Подтвердите что Вы не робот');
            }
        }

        return $this->render(':default/front/page:booking.html.twig', [
            'hall' => $hall,
            'form' => $form->createView(),
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

    /**
     * @param string $response
     * @return mixed|string
     */
    private function googleRecaptchaVerifyer(string $response) {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->getParameter('recaptcha_verify_url'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>
                "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"secret\"\r\n\r\n".$this->getParameter('recaptcha_secret_key')."\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"response\"\r\n\r\n".$response."\r\n-----011000010111000001101001--\r\n",
            CURLOPT_HTTPHEADER => array(
                "content-type: multipart/form-data; boundary=---011000010111000001101001"
            ),
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }

    }
}