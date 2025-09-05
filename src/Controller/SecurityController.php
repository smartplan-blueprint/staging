<?php

//// src/Controller/SecurityController.php
//namespace App\Controller;
//
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
//use Doctrine\Persistence\ManagerRegistry;
//use App\Entity\User;
//use Symfony\Component\Security\Core\User\InMemoryUser;
//
//class SecurityController extends AbstractController
//{
//
//    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
//    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
//    {
//        if ($this->getUser()) {
//            return $this->redirectToRoute('app_dashboard');
//        }
//
//        // get the login error if there is one
//        $error = $authenticationUtils->getLastAuthenticationError();
//        // last username entered by the user
//        $lastUsername = $authenticationUtils->getLastUsername();
//
//        return $this->render('security/login.html.twig', [
//            'last_username' => $lastUsername,
//            'error' => $error,
//        ]);
//    }
//    #[Route('/logout', name: 'app_logout')]
//    public function logout(): Response
//    {
//        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
//    }
//
//    #[Route('/session-info', name: 'app_session_info')]
//    public function sessionInfo(): Response
//    {
//        $session = $this->get('request_stack')->getCurrentRequest()->getSession();
//
//        return $this->json([
//            'session_id' => $session->getId(),
//            'session_name' => $session->getName(),
//            'session_data' => $session->all(),
//            'cookie_params' => session_get_cookie_params()
//        ]);
//    }
//}

// src/Controller/SecurityController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Symfony\Component\Security\Core\User\InMemoryUser;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            // Redirect based on user role after login
            if ($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_GROUP_ADMIN')) {
                return $this->redirectToRoute('app_dashboard_admin');
            } else {
                // For regular merchants, redirect to their dashboard
                // You might need to adjust this based on how you store merchant info
                $user = $this->getUser();
                if ($user && method_exists($user, 'getMerchant') && $user->getMerchant()) {
                    return $this->redirectToRoute('app_dashboard_merchant', ['id' => $user->getMerchant()->getId()]);
                }
                // Fallback to home if merchant info is not available
                return $this->redirectToRoute('app_dashboard_home');
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/session-info', name: 'app_session_info')]
    public function sessionInfo(): Response
    {
        $session = $this->get('request_stack')->getCurrentRequest()->getSession();

        return $this->json([
            'session_id' => $session->getId(),
            'session_name' => $session->getName(),
            'session_data' => $session->all(),
            'cookie_params' => session_get_cookie_params()
        ]);
    }
}
