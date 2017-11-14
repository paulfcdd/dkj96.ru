<?php

namespace AppBundle\Controller\Front;


use AppBundle\Entity as Entity;
use AppBundle\Form\BookingType;
use AppBundle\Service as Service;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation as Http;
use Symfony\Component\Routing\Annotation\Route;


class FrontController extends AppController
{
    /**
	 * @param Entity\Hall|null $hall
	 * @param Http\Request $request
	 * @return Http\Response
	 * @Route("/halls/booking/{hall}", name="halls.book_hall")
	 */
	public function bookHallAction(Entity\Hall $hall = null, Http\Request $request)
	{

		$doctrine = $this->getDoctrine();

		$form = $this->createForm(BookingType::class);

		if (!$hall) {
			$form->add('hall', EntityType::class, [
				'class' => Entity\Hall::class,
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

				/** @var Entity\Booking $formData */
				$formData = $form->getData();

				$hallTitle = null;

				if ($hall) {
					$hallTitle = $hall->getTitle();
				}

				if ($formData->getHall()) {
					$hallTitle = $formData->getHall()->getTitle();
				}
				/** @var Service\MailerService $mailer */
				$mailer = $this->get(Service\MailerService::class);

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
			'halls' => $doctrine->getRepository(Entity\Hall::class)->findAll(),
		]);
	}

    /**
     * @return Http\Response
     */
	public function renderBannerAction() {

		$banners = $this->getDoctrine()->getRepository(Entity\Banner::class)->findAll();

		return $this->render(':default/front/page/parts:banner.html.twig', [
			'banners' => $banners,
		]);
	}

	/**
	 * @param Entity\News $news
	 * @param Service\Utilities $utilities
	 * @return Http\Response
	 * @Route("/news/{news}", name="front.news")
	 * @Method({"POST", "GET"})
	 */
	public function showNewsAction(Entity\News $news = null, Service\Utilities $utilities, Http\Request $request)
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
			'reviews' => $em->getRepository(Entity\Review::class)->findBy(['approved' => 1, 'status' => 1]),
		]);

	}

	/**
	 * @Route("/event", name="event.list")
	 */
	public function eventListAction()
	{

		$em = $this->getDoctrine()->getManager();

		return $this->render(':default/front/page/event:list.html.twig', [
			'events' => $em->getRepository(Entity\Event::class)->findAll(),
			'metaTags' => $this->getMetaTags('event'),
		]);

	}

    /**
     * @param Http\Request $request
     * @param $form
     * @param $object
     */
    public function sendReview(Http\Request $request, $form, $object) {
        $response = $request->request->get('g-recaptcha-response');

        $em = $this->getDoctrine()->getManager();

        $resaptchaVerifyer = $this->googleRecaptchaVerifyer($response);

        $resaptchaVerifyer = json_decode($resaptchaVerifyer);

        if ($form->isValid() && $resaptchaVerifyer->success) {

            /** @var Entity\Review $formData */
            $formData = $form->getData();

            /** @var Service\MailerService $mailer */
            $mailer = $this->get(Service\MailerService::class);

            $mailer
                ->setTo($this->getParameter('client'))
                ->setFrom($formData->getEmail())
                ->setSubject('Новый отзыв о событии '.$object->getTitle())
                ->setBody($formData->getMessage());

            $formData->setEvent($object);

            $em->persist($formData);

            $em->flush();

            $mailer->sendMessage();

            $this->addFlash('success', 'Отзыв отправлен');

        } else {
            $this->addFlash('error', 'Вы должны подтвердить, что вы не робот');
        }
    }

}
