<?php

namespace AppBundle\Controller\Front;

use AppBundle\Service\FileUploaderService;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation as Http;
use AppBundle\Entity\Portfolio;

class PortfolioController extends FrontController
{
    /**
     * @return Http\Response
     * @Route("/portfolio", name="front.portfolio")
     */
    public function portfolioAction() {

        return $this->render(':default/front/page:portfolio.html.twig', [
            'objects' => $this->getDoctrine()->getRepository(Portfolio::class)->findAll(),
        ]);
    }

    /**
     * @param $portfolio
     * @return Http\Response
     * @Route("/portfolio/{portfolio}", name="front.portfolio.single")
     */
    public function singlePortfolioAction(Portfolio $portfolio) {
		
		if ($portfolio->isRedirect())
		{
			return $this->redirect($portfolio->getRedirectUrl());
		}
		
        return $this->render(':default/front/page/portfolio:single.html.twig', [
            'portfolio' => $portfolio,
            'imagesExt' => FileUploaderService::IMAGES,
            'videosExt' => FileUploaderService::VIDEOS,
        ]);

    }
}
