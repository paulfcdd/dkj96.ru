<?php

namespace AppBundle\Controller\Front;


use AppBundle\Entity\Artist;
use AppBundle\Entity\Banner;
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
use AppBundle\Service\FileUploaderService;
use AppBundle\Service\Utilities;
use AppBundle\Service\MailerService;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation as Http;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;


class FrontController extends AppController
{
    /**
	 * @param Hall|null $hall
	 * @param Http\Request $request
	 * @return Http\Response
	 * @Route("/halls/booking/{hall}", name="halls.book_hall")
	 */
	public function bookHallAction(Hall $hall = null, Http\Request $request)
	{

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
					return $hall->getTitle() . ' - ' . $hall->getCapacity() . ' чел.';
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

				$hallTitle = null;

				if ($hall) {
					$hallTitle = $hall->getTitle();
				}

				if ($formData->getHall()) {
					$hallTitle = $formData->getHall()->getTitle();
				}
				/** @var MailerService $mailer */
				$mailer = $this->get(MailerService::class);

				$mailer
					->setTo($this->getParameter('administrator'))
					->setFrom($formData->getEmail())
					->setSubject('Запрос на бронирование зала '.$hallTitle)
					->setBody($formData->getMessage());

				$doctrine->getManager()->persist($formData);

				try {
					$doctrine->getManager()->flush();
					$mailer->sendMessage();
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

	public function renderBannerAction() {

		$banners = $this->getDoctrine()->getRepository(Banner::class)->findAll();

		return $this->render(':default/front/page/parts:banner.html.twig', [
			'banners' => $banners,
		]);
	}

	/**
	 * @param News $news
	 * @param Utilities $utilities
	 * @return Http\Response
	 * @Route("/news/{news}", name="front.news")
	 * @Method({"POST", "GET"})
	 */
	public function showNewsAction(News $news = null, Utilities $utilities, Http\Request $request)
	{
		if ($news)
		{
			if ($news->isRedirect())
			{
				return $this->redirect($news->getRedirectUrl());
			}
		}

		$view = ':default/front/page/news:single.html.twig';

		$parameters = [
			'news' => $news,
            'imagesExt' => FileUploaderService::IMAGES,
            'videosExt' => FileUploaderService::VIDEOS,
		];

		if (!$news) {

			$paginator = $utilities
				->setObjectName(News::class)
				->setCriteria([])
				->setOrderBy(['publishStartDate' => 'DESC'])
				->setLimit(5)
				->setOffset(0);

			$repository = $this->getDoctrine()->getRepository(News::class);


			if ($request->isMethod('POST')) {

				$paginator
					->setLimit($request->get('limit'))
					->setOffset($request->get('offset'));

				return $this->render(':default/front/utility:paginator.html.twig', [
					'news' => $paginator->paginationAction(),
				]);
			}

			$view = ':default/front/page/news:list.html.twig';

			$parameters = [
				'news' => $repository->findBy([], ['publishStartDate' => 'DESC'], 5, 0),
				'paginator' => $paginator->getPages(),
				'offset' => $paginator->getOffset(),
				'limit' => $paginator->getLimit(),
			];
		}

		return $this->render($view, $parameters);
	}

	/**
	 * @return Http\Response
	 * @Route("/reviews", name="front.review_list")
	 */
	public function reviewListAction()
	{

		$em = $this->getDoctrine()->getManager();

		return $this->render(':default/front/page/review:list.html.twig', [
			'reviews' => $em->getRepository(Review::class)->findBy(['approved' => 1, 'status' => 1]),
		]);

	}

	/**
	 * @Route("/artists", name="front.artists")
	 */
	public function listArtistsPageAction()
	{
		return $this->render(':default/front/page:artisty.html.twig', [
			'artists' => $this->getDoctrine()->getRepository(Artist::class)->findAll(),
			'metaTags' => $this->getMetaTags('artist'),
		]);
	}

	/**
	 * @param $artist
	 * @return Http\Response
	 * @Route("/artists/{artist}", name="front.artists.detail")
	 */
	public function singleArtistAction(Artist $artist)
	{
		if ($artist)
		{
			if ($artist->isRedirect())
			{
				return $this->redirect($artist->getRedirectUrl());
			}
		}

		return $this->render(':default/front/page/artists:single.html.twig', [
			'artist' => $artist,
			'imagesExt' => FileUploaderService::IMAGES,
			'videosExt' => FileUploaderService::VIDEOS,
		]);
	}

	/**
	 * @Route("/event", name="event.list")
	 */
	public function eventListAction()
	{

		$em = $this->getDoctrine()->getManager();

		return $this->render(':default/front/page/event:list.html.twig', [
			'events' => $em->getRepository(Event::class)->findAll(),
			'metaTags' => $this->getMetaTags('event'),
		]);

	}

	/**
	 * @param Hall $hall
	 * @param Http\Request $request
	 * @return Http\Response
	 * @Route("/halls/{hall}/booking-calendar", name="halls.booking_calendar")
	 * @Method({"POST"})
	 */
	public function renderBookingsModalAction(Hall $hall, Http\Request $request)
	{

		return $this->render(':default/front/page/halls:hall_calendar_modal.html.twig', [
			'bookings' => $hall->getBookings()

		]);
	}
}
