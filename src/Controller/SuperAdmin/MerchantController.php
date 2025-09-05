<?php

//namespace App\Controller\SuperAdmin;
//
//use App\Entity\Merchant;
//use App\Entity\Merchant\MerchantDetails;
//use App\Form\SuperAdmin\MerchantType;
//use App\Repository\MerchantRepository;
//use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;
//
//#[Route('/admin/merchant')]
//class MerchantController extends AbstractController
//{
//    #[Route('/', name: 'app_merchant_index')]
//    public function index(MerchantRepository $merchantRepository): Response
//    {
//        $merchants = [];
//
//        if ($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_GROUP_ADMIN')) {
//            // Use query builder to eager load details
//            $merchants = $merchantRepository->createQueryBuilder('m')
//                ->leftJoin('m.merchantDetails', 'd')
//                ->addSelect('d')
//                ->getQuery()
//                ->getResult();
//        } else {
//            // Show only the merchant associated with the current user
//            $user = $this->getUser();
//            if ($user->getMerchant()) {
//                $merchants = [$user->getMerchant()];
//            }
//        }
//
//        return $this->render('superadmin/admin_index.html.twig', [
//            'merchants' => $merchants,
//        ]);
//    }
//
//    #[Route('/new', name: 'app_merchant_new', methods: ['GET', 'POST'])]
//    public function new(Request $request, EntityManagerInterface $entityManager): Response
//    {
//        $merchant = new Merchant();
//        $merchantDetails = new MerchantDetails();
//
//        // Set default values for ALL required fields
//        $merchantDetails->setGroupName('Default Group');
//        $merchantDetails->setContactName('Not Provided');
//        $merchantDetails->setContactEmail('not-provided@example.com');
//        $merchantDetails->setContactPhone('000-000-0000');
//
//        $merchant->setMerchantDetails($merchantDetails);
//        $merchantDetails->setMerchant($merchant);
//
//        $form = $this->createForm(MerchantType::class, $merchant);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            try {
//                // Double-check that all required fields are set
//                $merchantDetails = $merchant->getMerchantDetails();
//                if (empty($merchantDetails->getGroupName())) {
//                    $merchantDetails->setGroupName('Unnamed Group');
//                }
//                if (empty($merchantDetails->getContactName())) {
//                    $merchantDetails->setContactName('Not Provided');
//                }
//                if (empty($merchantDetails->getContactEmail())) {
//                    $merchantDetails->setContactEmail('not-provided@mail.com');
//                }
//                if (empty($merchantDetails->getContactPhone())) {
//                    $merchantDetails->setContactPhone('71234567');
//                }
//
//                $entityManager->persist($merchant);
//                $entityManager->flush();
//
//                $this->addFlash('success', 'Merchant created successfully.');
//                return $this->redirectToRoute('app_merchant_index');
//            } catch (\Exception $e) {
//                $this->addFlash('error', 'Merchant could not be created: ' . $e->getMessage());
//            }
//        }
//
//        return $this->render('superadmin/new_merchant.html.twig', [
//            'merchant' => $merchant,
//            'form' => $form->createView(),
//        ]);
//    }
//    #[Route('/{id}', name: 'app_merchant_show', methods: ['GET'])]
//    public function show(Merchant $merchant): Response
//    {
//        return $this->render('merchant/show.html.twig', [
//            'merchant' => $merchant,
//        ]);
//    }
//
//    #[Route('/{id}/edit', name: 'app_merchant_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Merchant $merchant, EntityManagerInterface $entityManager): Response
//    {
//        // Create MerchantDetails if it doesn't exist for edit
//        if (!$merchant->getMerchantDetails()) {
//            $merchantDetails = new MerchantDetails();
//            $merchant->setMerchantDetails($merchantDetails);
//        }
//
//        $form = $this->createForm(MerchantType::class, $merchant);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            // Ensure the bidirectional relationship is properly set
//            $merchantDetails = $merchant->getMerchantDetails();
//            if ($merchantDetails) {
//                $merchantDetails->setMerchant($merchant);
//            }
//
//            $entityManager->flush();
//
//            $this->addFlash('success', 'Merchant updated successfully.');
//
//            return $this->redirectToRoute('app_merchant_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('merchant/edit.html.twig', [
//            'merchant' => $merchant,
//            'form' => $form->createView(),
//        ]);
//    }
//
//    #[Route('/{id}/toggle-status', name: 'app_merchant_toggle_status', methods: ['POST'])]
//    public function toggleStatus(Request $request, Merchant $merchant, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('toggle-status' . $merchant->getId(), $request->request->get('_token'))) {
//            $merchant->setIsActive(!$merchant->getIsActive());
//            $entityManager->flush();
//
//            $status = $merchant->getIsActive() ? 'enabled' : 'disabled';
//            $this->addFlash('success', "Merchant {$status} successfully.");
//        }
//
//        return $this->redirectToRoute('app_merchant_index', [], Response::HTTP_SEE_OTHER);
//    }
//
//    #[Route('/{id}', name: 'app_merchant_delete', methods: ['POST'])]
//    public function delete(Request $request, Merchant $merchant, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete' . $merchant->getId(), $request->request->get('_token'))) {
//            $entityManager->remove($merchant);
//            $entityManager->flush();
//
//            $this->addFlash('success', 'Merchant deleted successfully.');
//        }
//
//        return $this->redirectToRoute('app_merchant_index', [], Response::HTTP_SEE_OTHER);
//    }
//
//}

namespace App\Controller\SuperAdmin;

use App\Entity\Merchant;
use App\Entity\Merchant\MerchantDetails;
use App\Entity\Merchant\PortalUserDetails;
use App\Entity\User;
use App\Form\SuperAdmin\MerchantType;
use App\Repository\MerchantRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

#[Route('/admin/merchant')]
class MerchantController extends AbstractController
{
    private $passwordHasher;
    private $mailer;
//    private string $twilioAccountSid; // Add Twilio config parameters
//        private string $twilioAuthToken;
//        private string $twilioPhoneNumber;

    public function __construct(UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer)
    {
        $this->passwordHasher = $passwordHasher;
        $this->mailer = $mailer;
//        $this->twilioAccountSid = $twilioAccountSid;
//        $this->twilioAuthToken = $twilioAuthToken;
//        $this->twilioPhoneNumber = $twilioPhoneNumber;
    }

    #[Route('/', name: 'app_merchant_index')]
    public function index(MerchantRepository $merchantRepository): Response
    {
        $merchants = [];

        if ($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_GROUP_ADMIN')) {
            // Use query builder to eager load details
            $merchants = $merchantRepository->createQueryBuilder('m')
                ->leftJoin('m.merchantDetails', 'd')
                ->addSelect('d')
                ->getQuery()
                ->getResult();
        } else {
            // Show only the merchant associated with the current user
            $user = $this->getUser();
            if ($user->getMerchant()) {
                $merchants = [$user->getMerchant()];
            }
        }

        return $this->render('superadmin/admin_index.html.twig', [
            'merchants' => $merchants,
        ]);
    }

    #[Route('/new', name: 'app_merchant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $merchant = new Merchant();
        $merchantDetails = new MerchantDetails();

        // Set default values for ALL required fields
        $merchantDetails->setGroupName('Default Group');
        $merchantDetails->setContactName('Not Provided');
        $merchantDetails->setContactEmail('not-provided@example.com');
        $merchantDetails->setContactPhone('000-000-0000');

        $merchant->setMerchantDetails($merchantDetails);
        $merchantDetails->setMerchant($merchant);

        $form = $this->createForm(MerchantType::class, $merchant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Double-check that all required fields are set
                $merchantDetails = $merchant->getMerchantDetails();
                if (empty($merchantDetails->getGroupName())) {
                    $merchantDetails->setGroupName('Unnamed Group');
                }
                if (empty($merchantDetails->getContactName())) {
                    $merchantDetails->setContactName('Not Provided');
                }
                if (empty($merchantDetails->getContactEmail())) {
                    $merchantDetails->setContactEmail('not-provided@mail.com');
                }
                if (empty($merchantDetails->getContactPhone())) {
                    $merchantDetails->setContactPhone('71234567');
                }

                $entityManager->persist($merchant);
                $entityManager->flush();

                // Create portal user and send email with temporary password
                $this->createPortalUserAndSendEmail($merchant, $entityManager);

                $this->addFlash('success', 'Merchant created successfully. Login credentials have been sent to the admin email.');
                return $this->redirectToRoute('app_merchant_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Merchant could not be created: ' . $e->getMessage());
            }
        }

        return $this->render('superadmin/new_merchant.html.twig', [
            'merchant' => $merchant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Generate a temporary password for the merchant admin
     */
    private function generateTemporaryPassword(): string
    {
        // Generate a 12-character password with uppercase, lowercase, numbers, and symbols
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_-=+;:,.?';

        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        $password = '';

        // Ensure at least one character from each set
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Fill the rest with random characters
        for ($i = 0; $i < 8; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password to make it more random
        return str_shuffle($password);
    }

    /**
     * Create portal user and send email with temporary password
     */
//    private function createPortalUserAndSendEmail(Merchant $merchant, EntityManagerInterface $entityManager): void
//    {
//        $merchantDetails = $merchant->getMerchantDetails();
//        $contactEmail = $merchantDetails->getContactEmail();
//        $contactName = $merchantDetails->getContactName();
//
//        // Generate temporary password
//        $temporaryPassword = $this->generateTemporaryPassword();
//
//        // Create portal user
//        $portalUser = new PortalUserDetails();
//        $portalUser->setMerchant($merchant);
//        $portalUser->setName($contactName);
//        $portalUser->setEmail($contactEmail);
//        $portalUser->setRoles(['ROLE_ADMIN']);
//        $portalUser->setEnabled(true);
//        $portalUser->setDateCreated(new \DateTime());
//
//        // Hash the password before storing
//        $hashedPassword = $this->passwordHasher->hashPassword($portalUser, $temporaryPassword);
//        $portalUser->setPassword($hashedPassword);
//
//        $entityManager->persist($portalUser);
//        $entityManager->flush();
//
//        // Send email with temporary password
//        $this->sendWelcomeEmail($contactEmail, $contactName, $temporaryPassword, $merchant->getName());
//    }


    private function createPortalUserAndSendEmail(Merchant $merchant, EntityManagerInterface $entityManager): void
    {
        $merchantDetails = $merchant->getMerchantDetails();
        $contactEmail = $merchantDetails->getContactEmail();
        $contactName = $merchantDetails->getContactName();
        $contactPhone = $merchantDetails->getContactPhone();

        // Generate temporary password
        $temporaryPassword = $this->generateTemporaryPassword();

        // Create user (instead of PortalUserDetails)
        $user = new User();
        $user->setMerchant($merchant);
        $user->setName($contactName);
        $user->setEmail($contactEmail);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setEnabled(true);
        $user->setDate_created(new \DateTime());

        // Hash the password before storing
        $hashedPassword = $this->passwordHasher->hashPassword($user, $temporaryPassword);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        // Send email with temporary password
        $this->sendWelcomeEmail($contactEmail, $contactName, $temporaryPassword, $merchant->getName());
       // $smsSuccess = $this->sendWelcomeSMS($contactPhone, $contactName, $temporaryPassword, $merchant->getName(), $contactEmail);

        // Log results
//        $emailSuccess = 0;
//        if (!$emailSuccess && !$smsSuccess) {
//            error_log('Failed to send both email and SMS credentials for merchant: ' . $merchant->getName());
//        } elseif (!$emailSuccess) {
//            error_log('Failed to send email credentials, but SMS was sent for merchant: ' . $merchant->getName());
//        } elseif (!$smsSuccess) {
//            error_log('Failed to send SMS credentials, but email was sent for merchant: ' . $merchant->getName());
//        }
    }

    /**
     * Send welcome email with temporary password
     */

//    private function sendWelcomeEmail(string $email, string $name, string $password, string $merchantName): bool
//    {
//        try {
//            // Validate email address
//            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//                error_log("Invalid email address: $email");
//                return false;
//            }
//
//            // Create email content with fallback if template doesn't exist
//            try {
//                $htmlContent = $this->renderView('emails/merchant_welcome.html.twig', [
//                    'name' => $name,
//                    'email' => $email,
//                    'password' => $password,
//                    'merchantName' => $merchantName
//                ]);
//            } catch (\Exception $templateError) {
//                // Fallback to simple email content
//                error_log('Email template not found, using fallback: ' . $templateError->getMessage());
//                $htmlContent = "
//                <h2>Welcome to Smart Plan Blueprint</h2>
//                <p>Hello $name,</p>
//                <p>Your merchant account for <strong>$merchantName</strong> has been created!</p>
//                <p><strong>Email:</strong> $email</p>
//                <p><strong>Temporary Password:</strong> $password</p>
//                <p>Login at: https://portal.smartplanblueprint.com/login</p>
//                <p>Please change your password after first login.</p>
//            ";
//            }
//
//            $emailMessage = (new Email())
//                ->from(new Address('no-reply@smartplanblueprint.com', 'Smart Plan Blueprint'))
//                ->to($email)
//                ->subject("Your $merchantName Merchant Account - Login Details")
//                ->html($htmlContent);
//
//            $this->mailer->send($emailMessage);
//
//            error_log("Welcome email successfully sent to: $email");
//            return true;
//
//        } catch (\Exception $e) {
//            error_log('Failed to send welcome email to ' . $email . ': ' . $e->getMessage());
//            return false;
//        }
//    }

    private function sendWelcomeEmail(string $email, string $name, string $password, string $merchantName): bool
    {
        try {
            // Validate email address
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error_log("Invalid email address: $email");
                return false;
            }

            // Debug: Log that we're attempting to send
            error_log("Attempting to send email to: $email");

            // Create email content
            $htmlContent = $this->renderView('emails/merchant_welcome.html.twig', [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'merchantName' => $merchantName
            ]);

            $emailMessage = (new Email())
                ->from(new Address('no-reply@smartplanblueprint.com', 'Smart Plan Blueprint'))
                ->to($email)
                ->subject("Your $merchantName Merchant Account - Login Details")
                ->html($htmlContent);

            // Add debug headers for testing
            $emailMessage->getHeaders()->addTextHeader('X-Debug', 'MerchantWelcomeEmail');

            $this->mailer->send($emailMessage);

            error_log("Welcome email successfully sent to: $email");
            return true;

        } catch (\Exception $e) {
            // More detailed error logging
            error_log('Email sending failed:');
            error_log('Recipient: ' . $email);
            error_log('Error: ' . $e->getMessage());
            error_log('Trace: ' . $e->getTraceAsString());

            return false;
        }
    }

    #[Route('/test-email', name: 'test_email')]
    public function testEmail(): Response
    {
        try {
            $email = (new Email())
                ->from('no-reply@smartplanblueprint.com')
                ->to('l.matekenya9@gmail.com') // Use a real email for testing
                ->subject('Test Email')
                ->text('This is a test email from Symfony Mailer');

            $this->mailer->send($email);

            return new Response('Test email sent successfully!');
        } catch (\Exception $e) {
            return new Response('Email failed: ' . $e->getMessage());
        }
    }

    /**
     * Send welcome SMS with temporary password
     */
//    private function sendWelcomeSMS(string $phoneNumber, string $name, string $password, string $merchantName, string $email): bool
//    {
//        try {
//            // Format phone number (remove any non-digit characters)
//            $formattedPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
//
//            // Add country code if missing (assuming Botswana +267)
//            if (!str_starts_with($formattedPhone, '267') && strlen($formattedPhone) === 8) {
//                $formattedPhone = '267' . $formattedPhone;
//            }
//
//            // Add + prefix for international format
//            $formattedPhone = '+' . $formattedPhone;
//
//            // Create Twilio client
//            $twilio = new Client($this->twilioAccountSid, $this->twilioAuthToken);
//
//            // Prepare SMS message
//            $message = "Hello $name! Your $merchantName merchant account has been created. " .
//                "Username: $email, Temp Password: $password. " .
//                "Login at: https://portal.smartplanblueprint.com - Please change your password after first login.";
//
//            // Send SMS
//            $twilio->messages->create(
//                $formattedPhone, // To
//                [
//                    'from' => $this->twilioPhoneNumber,
//                    'body' => $message
//                ]
//            );
//
//            return true;
//        } catch (\Exception $e) {
//            error_log('Failed to send welcome SMS: ' . $e->getMessage());
//            return false;
//        }
//    }

    #[Route('/{id}', name: 'app_merchant_view', methods: ['GET'])]
    public function show(Merchant $merchant): Response
    {
        return $this->render('superadmin/dashboard_actions/admin_merchant_view.html.twig', [
            'merchant' => $merchant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_merchant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Merchant $merchant, EntityManagerInterface $entityManager): Response
    {
        // Create MerchantDetails if it doesn't exist for edit
        if (!$merchant->getMerchantDetails()) {
            $merchantDetails = new MerchantDetails();
            $merchant->setMerchantDetails($merchantDetails);
        }

        $form = $this->createForm(MerchantType::class, $merchant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ensure the bidirectional relationship is properly set
            $merchantDetails = $merchant->getMerchantDetails();
            if ($merchantDetails) {
                $merchantDetails->setMerchant($merchant);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Merchant updated successfully.');

            return $this->redirectToRoute('app_merchant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('superadmin/dashboard_actions/admin_merchant_edit.html.twig', [
            'merchant' => $merchant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/toggle-status', name: 'app_merchant_toggle_status', methods: ['POST'])]
    public function toggleStatus(Request $request, Merchant $merchant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('toggle-status' . $merchant->getId(), $request->request->get('_token'))) {
            $merchant->setIsActive(!$merchant->getIsActive());
            $entityManager->flush();

            $status = $merchant->getIsActive() ? 'enabled' : 'disabled';
            $this->addFlash('success', "Merchant {$status} successfully.");
        }

        return $this->redirectToRoute('app_merchant_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_merchant_delete', methods: ['POST'])]
    public function delete(Request $request, Merchant $merchant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $merchant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($merchant);
            $entityManager->flush();

            $this->addFlash('success', 'Merchant deleted successfully.');
        }

        return $this->redirectToRoute('app_merchant_index', [], Response::HTTP_SEE_OTHER);
    }

    // Add this method to your MerchantController

//    #[Route('/{id}/regenerate-password', name: 'app_merchant_regenerate_password', methods: ['POST'])]
//    public function regeneratePassword(Request $request, Merchant $merchant, EntityManagerInterface $entityManager): Response
//    {
//        if (!$this->isCsrfTokenValid('regenerate-password' . $merchant->getId(), $request->request->get('_token'))) {
//            return $this->redirectToRoute('app_merchant_view', ['id' => $merchant->getId()]);
//        }
//
//        try {
//            // Find the admin user for this merchant
//            $userRepository = $entityManager->getRepository(User::class);
//            $adminUser = $userRepository->findOneBy([
//                'merchant' => $merchant,
//                'roles' => ['ROLE_ADMIN']
//            ]);
//
//            if (!$adminUser) {
//                $this->addFlash('error', 'No admin user found for this merchant.');
//                return $this->redirectToRoute('app_merchant_view', ['id' => $merchant->getId()]);
//            }
//
//            // Generate new temporary password
//            $newPassword = $this->generateTemporaryPassword();
//
//            // Hash and set the new password
//            $hashedPassword = $this->passwordHasher->hashPassword($adminUser, $newPassword);
//            $adminUser->setPassword($hashedPassword);
//            $adminUser->setIsTempPassword(true);
//            $adminUser->setPasswordChangedAt(null);
//
//            $entityManager->flush();
//
//            // Send email with new password
//            $merchantDetails = $merchant->getMerchantDetails();
//            $this->sendPasswordResetEmail(
//                $merchantDetails->getContactEmail(),
//                $merchantDetails->getContactName(),
//                $newPassword,
//                $merchant->getName()
//            );
//
//            $this->addFlash('success', 'New password generated and sent to the merchant admin.');
//
//        } catch (\Exception $e) {
//            $this->addFlash('error', 'Failed to regenerate password: ' . $e->getMessage());
//        }
//
//        return $this->redirectToRoute('app_merchant_view', ['id' => $merchant->getId()]);
//    }

    #[Route('/{id}/regenerate-password', name: 'app_merchant_regenerate_password', methods: ['POST'])]
    public function regeneratePassword(Request $request, Merchant $merchant, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!$this->isCsrfTokenValid('regenerate-password' . $merchant->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('app_merchant_view', ['id' => $merchant->getId()]);
        }

        try {
            // Find the admin user for this merchant - FIXED QUERY
            $userRepository = $entityManager->getRepository(User::class);

            // Get all users for this merchant and filter for admin role
            $users = $userRepository->findBy(['merchant' => $merchant]);
            $adminUser = null;

            foreach ($users as $user) {
                if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                    $adminUser = $user;
                    break;
                }
            }

            if (!$adminUser) {
                $this->addFlash('error', 'No admin user found for this merchant.');
                return $this->redirectToRoute('app_merchant_view', ['id' => $merchant->getId()]);
            }

            // Generate new temporary password
            $newPassword = bin2hex(random_bytes(8)); // Generate 16-character password

            // Hash and set the new password
            $hashedPassword = $passwordHasher->hashPassword($adminUser, $newPassword);
            $adminUser->setPassword($hashedPassword);
            $adminUser->setIsTempPassword(true);
            $adminUser->setPasswordChangedAt(null);

            $entityManager->flush();

            // Send email with new password - you'll need to implement this method
            $merchantDetails = $merchant->getMerchantDetails();
            if ($merchantDetails && $merchantDetails->getContactEmail()) {
                // Implement your email sending logic here
                // $this->sendPasswordResetEmail($merchantDetails->getContactEmail(), $merchantDetails->getContactName(), $newPassword, $merchant->getName());
                $this->addFlash('success', 'New password generated: ' . $newPassword . ' (Email functionality to be implemented)');
            } else {
                $this->addFlash('success', 'New password generated: ' . $newPassword . ' - No contact email available to send notification');
            }

        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to regenerate password: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_merchant_view', ['id' => $merchant->getId()]);
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail(string $email, string $name, string $password, string $merchantName): void
    {
        try {
            $emailMessage = (new Email())
                ->from(new Address('no-reply@smartplanblueprint.com', 'Smart Plan Blueprint'))
                ->to($email)
                ->subject('Your Password Has Been Reset')
                ->html($this->renderView('emails/reset.html.twig', [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'merchantName' => $merchantName
                ]));

            $this->mailer->send($emailMessage);
        } catch (\Exception $e) {
            error_log('Failed to send password reset email: ' . $e->getMessage());
            throw $e; // Re-throw to handle in controller
        }
    }
}
