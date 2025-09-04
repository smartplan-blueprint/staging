<?php
//
//namespace App\Service;
//
//
//use Symfony\Bundle\SecurityBundle\Security;
//use Symfony\Component\HttpFoundation\RequestStack;
//
//
//class MerchantContext
//{
//    private $requestStack;
//    private $security;
//
//    public function __construct(RequestStack $requestStack, Security $security)
//    {
//        $this->requestStack = $requestStack;
//        $this->security = $security;
//    }
//
//    public function getCurrentMerchant()
//    {
//        $request = $this->requestStack->getCurrentRequest();
//
//        if (!$request || !$request->hasSession()) {
//            return null;
//        }
//
//        $session = $request->getSession();
//        $merchant = $session->get('current_merchant');
//
//        // If no merchant in session, try to get from user context
//        if (!$merchant && $this->security->getUser()) {
//            $user = $this->security->getUser();
//            // Assuming user has getDefaultMerchant() method
//            if (method_exists($user, 'getDefaultMerchant')) {
//                $merchant = $user->getDefaultMerchant();
//                $this->setCurrentMerchant($merchant);
//            }
//        }
//
//        return $merchant;
//    }
//
//    public function setCurrentMerchant($merchant): void
//    {
//        $request = $this->requestStack->getCurrentRequest();
//
//        if ($request && $request->hasSession()) {
//            $session = $request->getSession();
//            $session->set('current_merchant', $merchant);
//        }
//    }
//
//    public function clearCurrentMerchant(): void
//    {
//        $request = $this->requestStack->getCurrentRequest();
//
//        if ($request && $request->hasSession()) {
//            $session = $request->getSession();
//            $session->remove('current_merchant');
//        }
//    }
//}


// src/Service/MerchantContext.php
namespace App\Service;

use App\Entity\Merchant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;


class MerchantContext
{
    private $requestStack;
    private $security;
    private $entityManager;

    public function __construct(
        RequestStack           $requestStack,
        Security               $security,
        EntityManagerInterface $entityManager
    )
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function getCurrentMerchant(): ?Merchant
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request || !$request->hasSession()) {
            return null;
        }

        $session = $request->getSession();

        // Try to get merchant ID from session
        $merchantId = $session->get('current_merchant_id');
        if ($merchantId) {
            $merchant = $this->entityManager->getRepository(Merchant::class)->find($merchantId);
            if ($merchant) {
                return $merchant;
            }
        }

        // If no merchant in session, try to get from user context
        if ($this->security->getUser()) {
            $user = $this->security->getUser();
            // Assuming user has getDefaultMerchant() method or similar
            if (method_exists($user, 'getMerchants') && !empty($user->getMerchants())) {
                $merchant = $user->getMerchants()->first();
                $this->setCurrentMerchant($merchant);
                return $merchant;
            }
        }

        return null;
    }

    public function setCurrentMerchant(Merchant $merchant): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request && $request->hasSession()) {
            $session = $request->getSession();
            $session->set('current_merchant_id', $merchant->getId());
        }
    }

    public function clearCurrentMerchant(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request && $request->hasSession()) {
            $session = $request->getSession();
            $session->remove('current_merchant_id');
        }
    }

    public function getCurrentMerchantId(): ?int
    {
        $merchant = $this->getCurrentMerchant();
        return $merchant ? $merchant->getId() : null;
    }
}
