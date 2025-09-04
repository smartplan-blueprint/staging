<?php
//
//namespace App\Security;
//
//
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\HttpFoundation\RedirectResponse;
//use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
//use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
//use Symfony\Component\Routing\RouterInterface;
//use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
//
//class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
//{
//    private $router;
//    private $authorizationChecker;
//
//    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $authorizationChecker)
//    {
//        $this->router = $router;
//        $this->authorizationChecker = $authorizationChecker;
//    }
//
//    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse|RedirectResponse
//    {
//        $user = $token->getUser();
//
//        // Check if user has super admin role
//        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
//            return new RedirectResponse($this->router->generate('app_merchant_index'));
//        }
//
//        // Check if user has admin role
//        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
//            return new RedirectResponse($this->router->generate('app_dashboard'));
//        }
//
//        // Default redirect for regular users
//        return new RedirectResponse($this->router->generate('app_dashboard'));
//    }
//}


// src/Security/AuthenticationSuccessHandler.php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;
    private $authorizationChecker;

    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse|RedirectResponse
    {
        $user = $token->getUser();
        $roles = $user->getRoles();

        // Check if user has super admin role
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN', $roles)) {
            return new RedirectResponse($this->router->generate('app_merchant_index'));
        }

//        // Check if user has admin role
//        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
//            return new RedirectResponse($this->router->generate('app_dashboard_index'));
//        }

        // Default redirect for regular users to their merchant dashboard
        return new RedirectResponse($this->router->generate('app_dashboard'));
    }
}
