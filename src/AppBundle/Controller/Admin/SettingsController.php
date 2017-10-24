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
		

		return $this->render(':default/admin:settings.html.twig', [
			'pageSeo' => $this->getStaticPageSeo(),
		]);
	}	
}
