<?php

namespace Sunnerberg\SimilarSeriesBundle\Helper;

use Sunnerberg\SimilarSeriesBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserAuthenticator {

    private $tokenStorage;

    function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function authenticate(User $user)
    {
        $token = new UsernamePasswordToken(
            $user,
            $user->getPassword(),
            'main',
            $user->getRoles()
        );
        $this->tokenStorage->setToken($token);
    }


}
