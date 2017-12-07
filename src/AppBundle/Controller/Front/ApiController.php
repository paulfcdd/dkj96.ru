<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Event;
use AppBundle\Entity\EventDateTime;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation as HTTP;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Service\Utilities;


/**
 * @Method({"POST"})
 */
class ApiController extends AppController
{
    public function em()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param HTTP\Request $request
     * @return HTTP\Response
     * @Route("/api/events-by-month", name="api.load-events-by-month")
     */
    public function loadEventsByMonthAction(HTTP\Request $request) {

        $requestParams = $request->request;

        $currentDate = new \DateTime();

        $currentMonth = $currentDate->format('m');

        $currentYear = $currentDate->format('Y');

        $translator = $this->get('translator');

        $firstDay = \DateTime::createFromFormat(self::DATE_FORMAT,$requestParams->get('firstDay'));

        if ($currentMonth == $firstDay->format('m') && $currentYear == $firstDay->format('Y')) {
            $firstDay = new \DateTime();
        }

        $lastDay = \DateTime::createFromFormat(self::DATE_FORMAT, $requestParams->get('lastDay'));

        /** @var EntityManager $qb */
        $qb = $this->em();

        $query = $qb->createQueryBuilder()
            ->select('e')
            ->from('AppBundle:EventDateTime', 'e')
            ->where('e.date BETWEEN :firstDay AND :lastDay')
            ->setParameters([
                'firstDay' => $firstDay,
                'lastDay' => $lastDay
            ])
            ->orderBy('e.date', 'ASC')
            ->addOrderBy('e.time', 'ASC')
            ->getQuery();

        $result = $query->getResult();

        $groupByDays = [];

        /** @var EventDateTime $item */
        foreach ($result as $item) {

            $key = $item->getDate()->format('j');

            if (!array_key_exists($key, $groupByDays)) {

                $groupByDays[$key]['date'] = $item->getDate()->format('j') . ' ' . $translator->trans('front.event.calendar.months.'. $item->getDate()->format('n'));
                $groupByDays[$key]['day'] = $translator->trans('front.event.calendar.days.'.$item->getDate()->format('N'));
                $groupByDays[$key]['events'] = [];
            }

            $event = [];

            $event['id'] = $item->getId();
            $event['dayNum'] = $item->getDate()->format('j');
            $event['dayName'] = $translator->trans('front.event.calendar.days.'.$item->getDate()->format('N'));
            $event['month'] = $translator->trans('front.event.calendar.months.'. $item->getDate()->format('n'));
            $event['price'] = $item->getEvent()->getPrice();
            $event['name'] = $item->getEvent()->getTitle();
            $event['time'] = $item->getTime()->format('H:i');
            $event['ticketUrl'] = $item->getEvent()->getTicketUrl();
            $event['slug'] = $item->getEvent()->getSlug() ?? $item->getEvent()->getId();
            $event['widgetCssCode'] = $item->getEvent()->getWidgetCssCode();
            $event['widgetHtmlCode'] = $item->getEvent()->getWidgetHtmlCode();
            $event['widgetJsCode'] = $item->getEvent()->getWidgetJsCode();

            array_push($groupByDays[$key]['events'], $event);

        }

        return $this->render(':default/front/page/event:calendar.html.twig', [
            'events' => $groupByDays,
            'entity' => 'event'
        ]);
    }

    /**
     * @param HTTP\Request $request
     * @param Utilities $utilities
     * @return HTTP\Response
     * @param HTTP\Request $request
     * @Route("/api/switch-page", name="api.switch-page")
     */
    public function switchPageAction(HTTP\Request $request, Utilities $utilities)
    {
		$paginator = $utilities
				->setObjectName($this->getClassName($request->request->get('entity')))
				->setCriteria([])
				->setOrderBy(['publishStartDate' => 'DESC'])
				->setLimit($request->request->get('limit'))
				->setOffset($request->request->get('offset'));
				
		return $this->render(':default/front/utility:paginator.html.twig', [
					'objects' => $paginator->paginationAction(),
				]);
	}
}
