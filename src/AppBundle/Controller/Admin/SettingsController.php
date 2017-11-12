<?php

namespace AppBundle\Controller\Admin;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends AdminController
{
	
	/**
     * @Route("/admin/settings", name="admin.settings")
     */
	public function mainScreenAction() {
		
		$categoryData = $this->getEntityRepository('category')->findOneByEntity('index');
				
		return $this->render(':default/admin:settings.html.twig', [
			'pageSeo' => $this->getStaticPageSeo(),
			'yandex_code' => $this->getMetricsCode('yandex'),
			'google_code' => $this->getMetricsCode('google'),
			'categoryData' => $categoryData,
			'entity' => 'index',
		]);
	}	
}
