<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FindController extends Controller
{

    /**
     * @Route("/find", name="find", methods={"get"})
     */
    public function indexAction()
    {
        return $this->render('SunnerbergSimilarSeriesBundle:Default:index.html.twig');
    }

}
