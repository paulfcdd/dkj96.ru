<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\File;
use AppBundle\Entity\Hall;
use AppBundle\Entity\News;
use AppBundle\Entity\Review;
use AppBundle\Service\MailerService;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Booking;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiController
 * @package AppBundle\Controller\Admin
 * @Route("/admin/api")
 * @Method({"POST"})
 */
class ApiController extends AdminController
{
	
    /**
     * @param string|null $name
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    public function doctrineManager(string $name = null) {
        return $this->getDoctrine()->getManager($name);
    }

    /**
     * @return JsonResponse
     * @Route("/mark_as_unread/{id}/{entity}", name="admin.api.message_unread")
     */
    public function markAsUnreadAjaxAction($id, $entity) {

        $entityRepository = $this->getEntityRepository($entity)->findOneById($id);

        $entityRepository->setStatus(0);

        try {
            $this->doctrineManager()->flush();
            return JsonResponse::create('ok');
        } catch (DBALException $exception) {
            return JsonResponse::create('not ok', 500);
        }
    }

    /**
     * @param $id
     * @param $entity
     * @return JsonResponse
     * @Route("/confirm-action/{id}/{entity}", name="admin.api.confirm_action")
     */
    public function confirmAjaxAction($id, $entity) {

        $entityRepository = $this->getEntityRepository($entity)->findOneById($id);

        if ($entityRepository instanceof Booking) {
            return $this->confirmBooking($entityRepository);
        }

        if ($entityRepository instanceof Review) {
            $entityRepository->setApproved(true);
            $this->doctrineManager()->flush();
            return JsonResponse::create(true);
        }
    }
    /**
     * @Route("/object_delete/{object}/{id}", name="admin.api.object_delete")
     */
    public function deleteObjectAjaxAction($object, $id) {

        $objectClass = 'AppBundle\\Entity\\'.ucfirst($object);

        $objectEntity = $this->doctrineManager()->getRepository($objectClass)->findOneById($id);

        if ($objectEntity instanceof Hall) {

            /** @var EntityManager $em */
            $em = $this->doctrineManager()->getRepository(Booking::class);

            $qb = $em->createQueryBuilder('b')
                ->delete('AppBundle:Booking', 'b')
                ->where('b.hall = :hall')
                ->setParameter('hall', $objectEntity->getId())
                ->getQuery();

            $qb->getResult();
        }


        if ($this->deleteObjectRelatedFiles($objectClass, intval($id))) {
            $this->doctrineManager()->remove($objectEntity);
            $this->doctrineManager()->flush();
            return JsonResponse::create($objectClass);
        }


    }

    /**
     * @param $entity
     * @param $id
     * @return JsonResponse
     * @Route("/message_delete/{entity}/{id}", name="admin.api.message_delete")
     */
    public function deleteMessageAjaxAction($entity, $id) {

        $entityRepository = $this->getEntityRepository($entity)->findOneById($id);

        $this->doctrineManager()->remove($entityRepository);

        try {
            $this->doctrineManager()->flush();
            return JsonResponse::create();
        } catch (DBALException $exception) {
            return JsonResponse::create('not ok', 500);
        }

    }

    /**
     * @param Booking $booking
     * @return JsonResponse
     */
    protected function confirmBooking(Booking $booking) {

        $em = $this->getDoctrine()->getManager();

        $bookings = $em->getRepository(Booking::class)->findBy([
            'hall' => $booking->getHall()->getId(),
            'date' => $booking->getDate(),
            'booked' => 1
        ]);

        if (empty($bookings)) {
            $booking->setBooked(true);
            $mailer = $this->get(MailerService::class);
            $mailer
                ->setSubject('Подтверждение брони зала '.$booking->getHall()->getTitle())
                ->setFrom($this->getParameter('mail_from'))
                ->setTo($booking->getEmail())
                ->setBody('Ваше бронирование было подтверждено');


            try {
                $mailer->sendMessage();
                $this->doctrineManager()->flush();
                return JsonResponse::create(true);
            } catch (DBALException $exception) {
                return JsonResponse::create('not ok', 500);
            }
        } else {
            return JsonResponse::create(false);
        }
    }

    /**
     * @param File $file
     * @return JsonResponse
     * @Route("/set_image_as_default/{file}", name="admin.api.set_as_default")
     *
     */
    public function setImageAsDefaultAjaxAction(File $file) {

        $doctrine = $this->getDoctrine();

        $resp = [
            'data' => null,
            'status' => null
        ];

        $objectFiles = $doctrine->getRepository(File::class)->findBy([
            'entity' => $file->getEntity(),
            'foreignKey' => $file->getForeignKey()
        ]);

        foreach ($objectFiles as $objectFile) {
            if ($objectFile->isIsDefault() == 1) {
                $objectFile->setIsDefault(0);
            }
        }

        $file->setIsDefault(1);

        try {
            $doctrine->getManager()->flush();
            $resp['data'] = 'ok';
            $resp['status'] = 200;
        } catch (\Exception $exception) {
            $resp['data'] = 'not ok';
            $resp['status'] = 500;
        }

        return JsonResponse::create($resp['data'], $resp['status']);
    }

    /**
     * @param File $file
     * @return JsonResponse
     * @Route("/file_delete/{file}", name="admin.api.file_delete")
     */
    public function deleteFileAjaxAction(File $file) {

       parent::deleteFile($file);

       return JsonResponse::create();
		}

	/**
	 * @param Request $request
	 * @return Response
	 * @Route("/render_object_selector", name="admin.api.render_object_selector")
	 */
		public function renderObjectSelectorAction(Request $request) {

			$object = $request->request->get('category');

			$classFQN = $this->getClassName($object);

			$objects = $this->doctrineManager()->getRepository($classFQN)->findAll();

			return $this->render(':default/admin/parts:object_selector.html.twig', [
				'objects' => $objects
			]);
		}
		
		/**
		* @param Request $request
		* @Route("/save_main_page_seo_to_yml", name="admin.api.save_main_page_seo_to_yml")
		*/
		public function saveMainPageSeoToYmlAction(Request $request) 
		{
			$requestParams = $request->request;	
			
			$pageName = $requestParams->get('pageName') . '.yml';
			$config = $this->yamlParse($pageName, self::CONFIG_FILE_PATH);
			
			$config['seoTitle'] = $requestParams->get('seoTitle');
			$config['seoKeywords'] = $requestParams->get('seoKeywords');
			$config['seoDescription'] = $requestParams->get('seoDescription');
				
			$writeFile = $this->yamlDump($pageName, $config, self::CONFIG_FILE_PATH);
			
			if ($writeFile) {
					
				if ($requestParams->get('pageName') == 'index') {
					return $this->redirectToRoute('admin.settings');	
				}
				
				return $this->redirectToRoute('admin.list', ['entity' => $requestParams->get('pageName')]);	

				
			}
			
		}
		
		/**
		* @param Request $request
		* @Route("/save-metrics-code", name="admin.api.save_metrics_code")
		*/
		public function saveMetricsCodeAction(Request $request) 
		{
			$requestParams = $request->request;	
			
			$metricsFileName = $requestParams->get('metricsType') . '.yml';
			
			$metricsFile = $this->yamlParse($metricsFileName, self::METRICS_FILE_PATH);
			
			$metricsFile = $requestParams->get('metricsCode');
			
			$writeFile = $this->yamlDump($metricsFileName, $metricsFile, self::METRICS_FILE_PATH);

			if ($writeFile) {
				return $this->redirectToRoute('admin.settings');	
			}
		}
		
		/**
		* @param Request $request
		* @Route("/save-robots-txt", name="admin.api.save_robots_txt")
		*/		
		public function saveRobotsTxtAction(Request $request) 
		{
			$robotsTxt = self::ROBOTS_TXT;
			
			$content = $request->request->get('robotsForm');
			
			try {
				
				file_put_contents($robotsTxt, $content);
				return $this->redirectToRoute('admin.settings');	
			} catch(\Exception $e) {
				return Response::create('Cannot wrote to file '.$robotsTxt.'. Reason: <strong>'.$e->getMessage().'</strong>');
			}
			
			dump($content);
			die;
			
		}
}
