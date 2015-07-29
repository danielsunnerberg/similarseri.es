<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class StartController extends Controller
{

    /**
     * @Route("/", name="start", methods={"get"})
     */
    public function indexAction()
    {
        return $this->render('SunnerbergSimilarSeriesBundle:Start:start.html.twig');
    }

}
