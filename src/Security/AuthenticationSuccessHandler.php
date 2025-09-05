<?php

//// src/Security/AuthenticationSuccessHandler.php
//
//namespace App\Security;
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
//        $roles = $user->getRoles();
//
//        // Check if user has super admin role
//        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN', $roles)) {
//            return new RedirectResponse($this->router->generate('app_merchant_index'));
//        }
//
////        // Check if user has admin role
////        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
////            return new RedirectResponse($this->router->generate('app_dashboard_index'));
////        }
//
//        // Default redirect for regular users to their merchant dashboard
//        return new RedirectResponse($this->router->generate('app_dashboard'));
//    }
//}


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

        // Check if user has super admin or group admin role
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_GROUP_ADMIN')) {
            return new RedirectResponse($this->router->generate('app_dashboard_admin'));
        }

        // For regular merchant users, redirect to their merchant dashboard
        // You might need to adjust this based on how you get the merchant ID
        if (method_exists($user, 'getMerchant') && $user->getMerchant()) {
            return new RedirectResponse($this->router->generate('app_dashboard_merchant', [
                'id' => $user->getMerchant()->getId()
            ]));
        }

        // Fallback for users without merchant association
        return new RedirectResponse($this->router->generate('app_dashboard_home'));
    }
}
