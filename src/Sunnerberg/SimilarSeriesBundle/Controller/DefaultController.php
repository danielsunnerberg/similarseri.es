<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SunnerbergSimilarSeriesBundle:Default:index.html.twig', array('name' => $name));
    }
}
