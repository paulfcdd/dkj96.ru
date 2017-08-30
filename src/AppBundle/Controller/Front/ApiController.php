<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Event;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation as HTTP;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Method({"POST"})
 */
class ApiController extends FrontController
{

    const DATE_FORMAT = 'Y-m-d';

    public function em()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param HTTP\Request $request
     * @Route("/api/events-by-month", name="api.load-events-by-month")
     */
    public function loadEventsByMonthAction(HTTP\Request $request) {

        $requestParams = $request->request;

        $firstDay = \DateTime::createFromFormat(self::DATE_FORMAT,$requestParams->get('firstDay'));

        $lastDay = \DateTime::createFromFormat(self::DATE_FORMAT, $requestParams->get('lastDay'));

        /** @var EntityManager $repository */
        $repository = $this->em()->getRepository(Event::class);


        $foo = $repository->createQueryBuilder('e')
            ->where('e.eventDate BETWEEN :foo AND :bar')
            ->setParameter('foo', $firstDay)
            ->setParameter('bar', $lastDay)
            ->getQuery()
            ->getResult();


        var_dump($foo);
        die;

    }
}