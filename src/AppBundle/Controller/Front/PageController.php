<?php

namespace AppBundle\Controller\Front;

use Symfony\Bridge\Doctrine\Form\Type as FormType;
use Symfony\Component\HttpFoundation as Http;
use AppBundle\Service as Service;
use AppBundle\Entity as Entity;
use AppBundle\Form as Form;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class PageController extends AppController
{
    /**
     * @return Http\Response
     * @Route("/portfolio", name="front.portfolio")
     */
    public function portfolioAction() {

        return $this->render(':default/front/page:portfolio.html.twig', [
            'objects' => $this->getDoctrine()
                ->getRepository(Entity\Portfolio::class)
                ->findBy([], ['eventDate' => 'DESC']),
        ]);
    }

    /**
     * @param $portfolio
     * @return Http\Response
     * @Route("/portfolio/{portfolio}", name="front.portfolio.single")
     */
    public function singlePortfolioAction(Entity\Portfolio $portfolio) {

        if ($portfolio->isRedirect())
        {
            return $this->redirect($portfolio->getRedirectUrl());
        }

        return $this->render(':default/front/page/portfolio:single.html.twig', [
            'portfolio' => $portfolio,
            'imagesExt' => Service\FileUploaderService::IMAGES,
            'videosExt' => Service\FileUploaderService::VIDEOS,
        ]);

    }

    /**
     * @param Entity\Hall|null $hall
     * @param Http\Request $request
     * @return Http\Response
     * @Route("/hall/booking/{hall}", name="halls.book_hall")
     */
    public function bookHallAction(Entity\Hall $hall = null, Http\Request $request)
    {

        $doctrine = $this->getDoctrine();

        $form = $this->createForm(Form\BookingType::class);
        $form->remove('time');

        if (!$hall) {
            $form->add('hall', FormType\EntityType::class, [
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
     * @param string|null $entity
     * @param null $slug
     * @param Service\Utilities $utilities
     * @param Http\Request $request
     * @return Http\RedirectResponse|Http\Response
     * @Route("/{entity}/{slug}", name="front.load_page")
     * @Method({"POST", "GET"})
     */
    public function loadPageAction(string $entity = null, $slug = null, Service\Utilities $utilities, Http\Request $request)
    {
        $repository = $this->getEntityRepository($entity);

        $object = null;

        if ($slug) {

            if (!intval($slug)) {
                $object = $repository->findOneBySlug($slug);
            } else {
                $object = $repository->findOneById($slug);
            }
        }

        if ($object instanceof Entity\History)
        {
            $view = ':default/front/page/'.$entity.':show.html.twig';
            $parameters = [
                'history' => $this->getDoctrine()->getRepository(Entity\History::class)->findOneBy(['isEnabled' => true]),
                'categoryData' => $this->getEntityRepository('category')->findOneByEntity($entity),
            ];
            return $this->render($view, $parameters);
        }

        if (!$slug)
        {
            return $this->loadListAction($repository, $entity, $utilities, $request);
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
                'imagesExt' => Service\FileUploaderService::IMAGES,
                'videosExt' => Service\FileUploaderService::VIDEOS,
            ];

            if ($object instanceof Entity\Event) {

                $showBuyTicketBtn = false;

                $form = $this->createForm(Form\ReviewType::class)->handleRequest($request);

                if ($form->isSubmitted()) {

                    $this->sendReview($request, $form, $object);
                }

                if ($object->getEventDate() > new \DateTime()) {
                    $showBuyTicketBtn = true;
                }


                $parameters['showBuyTicketBtn'] = $showBuyTicketBtn;
                $parameters['form'] = $form->createView();
            }

            if ($object instanceof Entity\Hall) {

                $bookings = $this->getDoctrine()->getRepository(Entity\Booking::class)->findBy(
                    [
                        'hall' => $object,
                        'booked' => true,
                    ]);

                $parameters['bookings'] = $bookings;
            }

            return $this->render($view, $parameters);

        }
    }

    /**
     * @param $repository
     * @param $entityName
     * @param $utilities
     * @param $request
     * @return Http\Response
     * @Method({"POST", "GET"})
     */
    public function loadListAction($repository, $entityName, $utilities, $request)
    {


        $view = ':default/front/page/'.$entityName.':list.html.twig';

        $parameters = [
            'objects' => $repository->findAll(),
            'metaTags' => $this->getCategoryData($entityName),
            'entity' => $entityName,
            'categoryData' => $this->getEntityRepository('category')->findOneByEntity($entityName),
        ];

        if ($entityName == 'news') {

            $paginator = $utilities
                ->setObjectName(Entity\News::class)
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

        if ($entityName == 'history') {

            $history = $this->getEntityRepository($entityName)->findOneBy(['isEnabled' => true]);

            return $this->render(':default/front/page/history:show.html.twig', [
                'history' => $history,
                'categoryData' => $this->getEntityRepository('category')->findOneByEntity($entityName),

            ]);
        }

        return $this->render($view, $parameters);
    }

}