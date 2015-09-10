<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class RegisterController extends Controller {

    /**
     * @Route("/register", name="register_route", methods={"get", "post"})
     */
    public function loginAction(Request $request)
    {
        $newUser = new User();
        $form = $this->getRegisterForm($newUser);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // The user is set as plain-text by the form-utils, hash it before storage
            $this->encodeUserPassword($newUser);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newUser);
            $entityManager->flush();

            $userAuthenticator = $this->get('sunnerberg_similar_series.helper.user_authenticator');
            $response = new RedirectResponse($this->generateUrl('find'));
            $userAuthenticator->authenticate($newUser, $request, $response);

            return $response;
        }

        return $this->render(
            'SunnerbergSimilarSeriesBundle:Register:register.html.twig',
            ['form' => $form->createView()]
        );
    }

    private function getPlaceholderOptions($placeholderText)
    {
        return [
            'attr' => ['placeholder' => $placeholderText],
            'label' => false,
        ];
    }

    private function getRegisterForm($newUser)
    {
        return $this->createFormBuilder($newUser)
            ->add('username', 'text', $this->getPlaceholderOptions('Username'))
            ->add('password', 'repeated', [
                'type' => 'password',
                'first_options' => $this->getPlaceholderOptions('Password'),
                'second_options' => $this->getPlaceholderOptions('Repeat password'),
                'invalid_message' => 'The passwords must match.',
                'error_bubbling' => true
            ])
            ->add('submit', 'submit', ['label' => 'Register'])
            ->getForm()
        ;
    }

    private function encodeUserPassword($newUser)
    {
        $encoder = $this->get('security.encoder_factory')->getEncoder($newUser);
        $encodedPassword = $encoder->encodePassword($newUser->getPassword(), $newUser->getSalt());
        $newUser->setPassword($encodedPassword);
    }

}
