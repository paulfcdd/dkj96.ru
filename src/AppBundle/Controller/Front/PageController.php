<?php

namespace AppBundle\Controller\Front;

use Symfony\Component\HttpFoundation as Http;
use AppBundle\Service as Service;
use AppBundle\Entity as Entity;
use AppBundle\Form as Form;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class PageController extends AppController
{

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

        if ($entity == 'history')
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

        $object = null;

        if (!intval($slug)) {
            $object = $repository->findOneBySlug($slug);
        } else {
            $object = $repository->findOneById($slug);
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
                    $response = $request->request->get('g-recaptcha-response');

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

        return $this->render($view, $parameters);
    }

}