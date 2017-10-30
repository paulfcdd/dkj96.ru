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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation as Http;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;


class FrontController extends Controller
{
	const CONFIG_FILE_PATH = __DIR__.('/../../../../app/config/page/');
	
	const METRICS_FILE_PATH = __DIR__.('/../../../../app/config/metrics/');


	/**
	 * @param string | null $page
	 * @return Http\Response
	 * @Route("/", name="front.index")
	 */
	public function indexAction(string $page = null)
	{

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
			->orderBy('n.publishStartDate', 'DESC')
			->getQuery();
						
		return $this->render(':default/front/page:index.html.twig', [
			'page' => $page,
			'events' => $eventQB->getResult(),
			'news' => $newsQB->getResult(),
			'reviews' => $this->getSortedList(
				Review::class, ['dateReceived' => 'DESC'], new \DateTime(), null, 2
			),
			'metaTags' => $this->getMetaTags('index'),
		]);
	}
	
	/**
	 * @param Http\Request $request
	 * @return Http\Response
	 * @Route("/contact", name="front.contact")
	 */
	public function contactPageAction(Http\Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$form = $this
			->createForm(FeedbackType::class)
			->handleRequest($request);


		if ($form->isSubmitted()) {

			$response = $request->request->get('g-recaptcha-response');

			$recaptchaVerifyer = $this->googleRecaptchaVerifyer($response);

			$recaptchaVerifyer = json_decode($recaptchaVerifyer);

			if ($form->isValid() && $recaptchaVerifyer->success) {

				$formData = $form->getData();

				/** @var MailerService $mailer */
				$mailer = $this->get(MailerService::class);

				$mailer
					->setTo($this->getParameter($formData->getToWhom()))
					->setBody($formData->getMessage())
					->setFrom($formData->getEmail())
					->setSubject('Новое сообщение');

				if (strpos($formData->getToWhom(), 'client_')) {
				    $mailer->setSubject(Feedback::TO_WHOM[$formData->getToWhom()]);
                }

				$em->persist($formData);

				try {
					$em->flush();
					$mailer->sendMessage();
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
	 * @param string | integer | null $slug
	 * @Route("/{entity}/{slug}", name="front.load_page")
	 * @Method({"POST", "GET"})
	 */ 
	public function loadPageAction(string $entity = null, $slug = null, Utilities $utilities, Http\Request $request)
	{				
		$repository = $this->getEntityRepository($entity);
		
		if ($entity == 'history') 
		{
			$view = ':default/front/page/'.$entity.':show.html.twig';
			$parameters = [
				'history' => $this->getDoctrine()->getRepository(History::class)->findOneBy(['isEnabled' => true]),
				'metaTags' => $this->getMetaTags('history'),
			];
			return $this->render($view, $parameters);
		}
		
		if (!$slug) 
		{
			return $this->loadListAction($repository, $entity, $utilities, $request);	
		}
				
		$object = null;
		
		if (intval($slug)) {
			$object = $repository->findOneById($slug);
		} else {
			$object = $repository->findOneBySlug($slug);
		}
		
		if ($object) 
		{		
			if ($object->isRedirect())
			{
				return $this->redirect($object->getRedirectUrl());
			}
			
			$view = ':default/front/page/'.$entity.':single.html.twig';
			
			$parameters = [
				'object' => $object,
				'imagesExt' => FileUploaderService::IMAGES,
				'videosExt' => FileUploaderService::VIDEOS,
			];
			
			if ($object instanceof Event) {
					
				$showBuyTicketBtn = false;
				
				$form = $this->createForm(ReviewType::class)->handleRequest($request);
				
				if ($form->isSubmitted()) {
				$response = $request->request->get('g-recaptcha-response');

				$resaptchaVerifyer = $this->googleRecaptchaVerifyer($response);

				$resaptchaVerifyer = json_decode($resaptchaVerifyer);

				if ($form->isValid() && $resaptchaVerifyer->success) {

						/** @var Review $formData */
						$formData = $form->getData();

						/** @var MailerService $mailer */
						$mailer = $this->get(MailerService::class);

						$mailer
							->setTo($this->getParameter('client'))
							->setFrom($formData->getEmail())
							->setSubject('Новый отзыв о событии '.$event->getTitle())
							->setBody($formData->getMessage());

						$formData->setEvent($event);

						$em->persist($formData);

						$em->flush();

						$mailer->sendMessage();

						$this->addFlash('success', 'Отзыв отправлен');

					} else {
						$this->addFlash('error', 'Вы должны подтвердить, что вы не робот');
					}
				}
		
				if ($object->getEventDate() > new \DateTime()) {
					$showBuyTicketBtn = true;
				}
				
				
				$parameters['showBuyTicketBtn'] = $showBuyTicketBtn;
				$parameters['form'] = $form->createView();
			}
			
			return $this->render($view, $parameters);

		}		
	}
	
	/**
	 * @Method({"POST", "GET"})
	 */ 
	public function loadListAction($repository, $entityName, $utilities, $request) {
			
		
		$view = ':default/front/page/'.$entityName.':list.html.twig';
		
		$parameters = [
			'objects' => $repository->findAll(),
			'metaTags' => $this->getMetaTags($entityName),
			'entity' => $entityName,
		];
		
		if ($entityName == 'news') {
				
				$paginator = $utilities
					->setObjectName(News::class)
					->setCriteria([])
					->setOrderBy(['publishStartDate' => 'DESC'])
					->setLimit(5)
					->setOffset(0);
				if ($request->isMethod('POST')) {

					$paginator
						->setLimit($request->get('limit'))
						->setOffset($request->get('offset'));

					return $this->render(':default/front/utility:paginator.html.twig', [
						'news' => $paginator->paginationAction(),
					]);
				}
					
				$parameters['objects'] = $repository->findBy([], ['publishStartDate' => 'DESC'], 5, 0);
				$parameters['paginator'] = $paginator->getPages();
				$parameters['offset'] = $paginator->getOffset();
				$parameters['limit'] = $paginator->getLimit();
	
			}
		
		return $this->render($view, $parameters);
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
	 * @Route("/history", name="front.history")
	 */
	public function listHistoryPageAction()
	{

		return $this->render(':default/front/page:istoriya.html.twig', [
			'history' => $this->getDoctrine()->getRepository(History::class)->findOneBy(['isEnabled' => true]),
			'metaTags' => $this->getMetaTags('history'),
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
	 * @param $event
	 * @param $request
	 * @return Http\Response
	 * @Route(
	 *     "/event/{event}",
	 *     name="event.details_page"
	 * )
	 * @Method({"POST", "GET"})
	 */
	public function eventDetailAction(Event $event, Http\Request $request)
	{
		if ($event) 
		{
			if ($event->isRedirect())
			{
				return $this->redirect($event->getRedirectUrl());
			}
		}

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

				/** @var Review $formData */
				$formData = $form->getData();

				/** @var MailerService $mailer */
				$mailer = $this->get(MailerService::class);

				$mailer
					->setTo($this->getParameter('client'))
					->setFrom($formData->getEmail())
					->setSubject('Новый отзыв о событии '.$event->getTitle())
					->setBody($formData->getMessage());

				$formData->setEvent($event);

				$em->persist($formData);

				$em->flush();

				$mailer->sendMessage();

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
	 * @Route("/halls", name="halls.list")
	 */
	public function listHallsAction()
	{

		$doctrine = $this->getDoctrine();

		return $this->render(':default/front/page:halls.html.twig', [
			'halls' => $doctrine->getRepository(Hall::class)->findAll(),
			'metaTags' => $this->getMetaTags('hall'),
		]);
	}

	/**
	 * @param Hall $hall
	 * @return Http\Response
	 * @Route("/halls/{hall}", name="halls.detail")
	 */
	public function hallInfoAction(Hall $hall)
	{
		if ($hall) 
		{
			if ($hall->isRedirect())
			{
				return $this->redirect($hall->getRedirectUrl());
			}
		}
		
		
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
	
	public function getMetricsCodeAction($metricsName) 
	{
		$metricsFile = self::METRICS_FILE_PATH . $metricsName . '.yml';
		
		if (!file_exists($metricsFile)) {
			copy(self::METRICS_FILE_PATH.'default.yml', $metricsFile);	
		}
		
		$metricsContent = Yaml::parse(file_get_contents($metricsFile));
		
		return Http\Response::create($metricsContent);
	}

	/**
	 * @param string $class
	 * @param array $orderBy
	 * @param \DateTime $filterDate
	 * @param string $selectField
	 * @param int|null $limit
	 * @return array
	 */
	private function getSortedList(string $class, array $orderBy, \DateTime $filterDate = null, string $selectField = null, int $limit = null)
	{

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


		$qb = $qb
			->orderBy('a.' . key($orderBy), current($orderBy))
			->getQuery();

		return $qb->getResult();
	}

	/**
	 * @param string $response
	 * @return mixed|string
	 */
	private function googleRecaptchaVerifyer(string $response)
	{

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
				"-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"secret\"\r\n\r\n" . $this->getParameter('recaptcha_secret_key') . "\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"response\"\r\n\r\n" . $response . "\r\n-----011000010111000001101001--\r\n",
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
	
	private function getMetaTags(string $pageName) {
		
		$file = self::CONFIG_FILE_PATH . $pageName . '.yml';
		
		if (!file_exists($file)) {
			copy(self::CONFIG_FILE_PATH.'default.yml', $file);	
		}
		
		$metaTags = Yaml::parse(file_get_contents($file));
		
		return $metaTags;
	}
	
	/**
     * @param string $entity
     * @return string
     */
    protected function getClassName(string $entity) {

        $className = ucfirst($entity);

        $class = 'AppBundle\\Entity\\'.$className;

        return $class;
    }

    /**
     * @param string $entity
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getEntityRepository(string $entity) {

        return $this->getDoctrine()->getRepository($this->getClassName($entity));

    }
}
