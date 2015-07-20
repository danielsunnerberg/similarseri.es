<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoginController extends Controller {

    /**
     * @Route("/login", name="login_route", methods={"get"})
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'SunnerbergSimilarSeriesBundle:Login:login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }

    /**
     * @Route("/login/check", name="login_check", methods="post")
     */
    public function loginCheckAction()
    {
        $this->redirectToRoute('start');
    }

}
