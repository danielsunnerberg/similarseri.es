<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
     * @Route("/login/anonymous", name="anonymous_login", methods="get")
     */
    public function anonymousLoginAction()
    {
        $anonymousUser = new User();
        $anonymousUser->setUsername($this->generateRandomUsername());
        $anonymousUser->setLocked(true);
        // A password is not needed, as no one can login to the account later, since it is marked as locked
        $anonymousUser->setPassword('');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($anonymousUser);
        $entityManager->flush();

        $token = new UsernamePasswordToken(
            $anonymousUser,
            $anonymousUser->getPassword(),
            'main',
            $anonymousUser->getRoles()
        );
        $this->get('security.token_storage')->setToken($token);

        return $this->redirectToRoute('find');
    }

    private function generateRandomUsername()
    {
        return 'anonymous_user-' . bin2hex(openssl_random_pseudo_bytes(32));
    }

}
