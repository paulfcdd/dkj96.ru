<?php

namespace AppBundle\Controller\Front;

use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation as Http;
use AppBundle\Entity as Entity;


class MainPageController extends AppController
{
    /**
     * @param string | null $page
     * @return Http\Response
     * @Route("/", name="front.index")
     */
    public function indexAction(string $page = null)
    {

        /** @var EntityManager $eventRepo */
        $eventRepo = $this->getDoctrine()->getRepository(Entity\EventDateTime::class);

        /** @var EntityManager $newsRepo */
        $newsRepo = $this->getDoctrine()->getRepository(Entity\News::class);

        $eventQB = $eventRepo->createQueryBuilder('e')
            ->where('e.date > :filterdate')
            ->setParameter('filterdate', new \DateTime())
            ->setMaxResults(6)
            ->orderBy('e.date', 'ASC')
            ->getQuery();

        $newsQB = $newsRepo->createQueryBuilder('n')
            ->where(':filterdate BETWEEN n.publishStartDate AND n.publishEndDate')
            ->setParameter('filterdate', new \DateTime())
            ->setMaxResults(6)
            ->orderBy('n.publishStartDate', 'DESC')
            ->getQuery();


        return $this->render(':default/front/page:index.html.twig', [
            'page' => $page,
            'eventsDate' => $eventQB->getResult(),
            'news' => $newsQB->getResult(),
            'reviews' => $this->getSortedList(
                Entity\Review::class, ['dateReceived' => 'DESC'], new \DateTime(), null, 2
            ),
            'categoryData' => $this->getCategoryData('index'),
        ]);
    }
}