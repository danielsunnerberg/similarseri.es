<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="start", methods={"get"})
     */
    public function indexAction()
    {
        return $this->render('SunnerbergSimilarSeriesBundle:Default:index.html.twig');
    }

}
