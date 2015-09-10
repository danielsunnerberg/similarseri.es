<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Entity\User;
use Sunnerberg\SimilarSeriesBundle\Helper\RandomGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
        return $this->redirectToRoute('find');
    }

    /**
     * @Route("/login/anonymous", name="anonymous_login", methods="post")
     */
    public function anonymousLoginAction(Request $request)
    {
        $anonymousUser = new User();
        $anonymousUser->setUsername(RandomGenerator::generateRandomUsername());
        $anonymousUser->setPassword(RandomGenerator::generateRandomString(64));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($anonymousUser);
        $entityManager->flush();

        $userAuthenticator = $this->get('sunnerberg_similar_series.helper.user_authenticator');
        $response = new RedirectResponse($this->generateUrl('find'));
        $userAuthenticator->authenticate($anonymousUser, $request, $response);

        return $response;
    }

}
