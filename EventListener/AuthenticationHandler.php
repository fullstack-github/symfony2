<?php

namespace Dusk\UserBundle\EventListener;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Routing\RouterInterface;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface {

    private $router;

    /**
     * Constructor
     * @param RouterInterface   $router
     */
    public function __construct(RouterInterface $router) {
        $this->router = $router;
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from AbstractAuthenticationListener.
     * @param Request $request
     * @param TokenInterface $token
     * @return Response The response to return
     */
    function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        $user = $token->getUser();
        
        if ($user->hasRole('ROLE_SUPER_ADMIN')) {
            $uri = $this->router->generate('fos_user_security_logout');
            return new RedirectResponse($this->router->generate('fos_user_security_logout'));
        } else {
            $uri = $this->router->generate('dusk_mymusic');
        }
        
        return new RedirectResponse($uri);
    }

    function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        var_dump($request);
        die();
    }

    public function onLogoutSuccess(Request $request) {
        
    }

}
