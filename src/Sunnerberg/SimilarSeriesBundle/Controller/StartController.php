<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StartController extends Controller
{

    /**
     * @Route("/", name="start", methods={"get"})
     */
    public function indexAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('find');
        }
        
        return $this->render('SunnerbergSimilarSeriesBundle:Start:start.html.twig');
    }

}
