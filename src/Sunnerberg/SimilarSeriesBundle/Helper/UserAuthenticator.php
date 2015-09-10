<?php

namespace Sunnerberg\SimilarSeriesBundle\Helper;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sunnerberg\SimilarSeriesBundle\Entity\User;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;

/**
 * Provides tools to programmatically authenticate and login a specified user.
 *
 * Class UserAuthenticator
 * @package Sunnerberg\SimilarSeriesBundle\Helper
 */
class UserAuthenticator {

    private $managerRegistry;
    private $key;

    const USER_ENTITY = 'Sunnerberg\SimilarSeriesBundle\Entity\User';

    public function __construct(ManagerRegistry $managerRegistry, $key)
    {
        $this->managerRegistry = $managerRegistry;
        $this->key = $key;
    }

    /**
     * Authenticates and logs in the specified user. The user will be remembered through a cookie.
     *
     * @param User $user
     * @param Request $request
     * @param Response $response
     * @param array $options
     */
    public function authenticate(User $user, Request $request, Response $response, array $options = null)
    {
        if (! $options) {
            $options = $this->getDefaultOptions();
        }

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $userProvider = new EntityUserProvider($this->managerRegistry, self::USER_ENTITY, 'username');
        $rememberMeService = new TokenBasedRememberMeServices([$userProvider], $this->key, 'main', $options);
        $rememberMeService->loginSuccess($request, $response, $token);
    }

    private function getDefaultOptions()
    {
        return [
            'path' => '/',
            'name' => 'REMEMBERME',
            'domain' => null,
            'secure' => false,
            'httponly' => true,
            'lifetime' => 31556926, // = 1 year
            'always_remember_me' => true,
        ];
    }

}
