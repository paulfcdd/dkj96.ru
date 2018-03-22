<?php

namespace AppBundle\Controller\Admin;


use AppBundle\Form\TopNavbarType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity as Entity;

class SettingsController extends AdminController
{
	
	/**
     * @Route("/admin/settings", name="admin.settings")
     * @Method({"POST", "GET"})
     */
	public function mainScreenAction(Request $request) {
		
		$categoryData = $this->getEntityRepository('category')->findOneByEntity('index');
		$topNavbar = $this->getEntityRepository('topNavbar')->findAll();
		$topNavbarForm = $this->createForm(TopNavbarType::class)->handleRequest($request);

		return $this->render(':default/admin:settings.html.twig', [
			'pageSeo' => $this->getStaticPageSeo(),
			'yandex_code' => $this->getMetricsCode('yandex'),
			'google_code' => $this->getMetricsCode('google'),
			'categoryData' => $categoryData,
			'entity' => 'index',
            'topNavabr' => $topNavbar,
            'faIcons' => array_flip(Entity\TopNavbar::ICONS),
            'form' => $topNavbarForm->createView(),
		]);
	}	
}
