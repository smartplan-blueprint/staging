<?php
//
//namespace App\Controller;
//
//// src/Controller/SecurityController.php
//namespace App\Controller;
//
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
//
//class SecurityController extends AbstractController
//{
//    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
//    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
//    {
//
//        // Debug form submission
//        if ($request->isMethod('POST')) {
//            dump([
//                'submitted_email' => $request->request->get('email'),
//                'submitted_password' => $request->request->get('password'),
//                'server_users' => $this->getParameter('security.user.providers.in_memory.memory.users')
//            ]);
//            return $this->redirectToRoute('app_dashboard');
//        }
//
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
//          //  'csrf_token' => null, // Handled automatically by Symfony
//        ]);
//    }
//
//    #[Route('/logout', name: 'app_logout')]
//    public function logout(): Response
//    {
//
////        return $this->render('security/login.html.twig');
//        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
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
            return $this->redirectToRoute('app_dashboard');
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
//
//class SecurityController extends AbstractController
//{
//    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
//    public function login(Request $request, AuthenticationUtils $authenticationUtils, ManagerRegistry $doctrine): Response
//    {
//        // Debug form submission - show both in-memory and database users
//        if ($request->isMethod('POST')) {
//            $email = $request->request->get('email');
//            $password = $request->request->get('password');
//
//            // Get in-memory users from parameters
//            $inMemoryUsers = $this->getParameter('security.user.providers.in_memory.memory.users');
//
//            // Get database user
//            $dbUser = $doctrine->getRepository(User::class)->findOneBy(['email' => $email]);
//
//            dump([
//                'submitted_email' => $email,
//                'submitted_password' => $password,
//                'in_memory_users' => array_keys($inMemoryUsers),
//                'database_user' => $dbUser ? [
//                    'email' => $dbUser->getEmail(),
//                    'roles' => $dbUser->getRoles(),
//                    'password_hash' => $dbUser->getPassword()
//                ] : null
//            ]);
//
//            return $this->redirectToRoute('app_dashboard');
//        }
//
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
//
//    #[Route('/logout', name: 'app_logout')]
//    public function logout(): Response
//    {
//        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
//    }
//}
