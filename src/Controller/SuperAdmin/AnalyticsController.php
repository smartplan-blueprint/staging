<?php

namespace App\Controller\SuperAdmin;


use App\Repository\Financials\CommissionEarnedRepository;
use App\Repository\Financials\FinancialsRepository;
use App\Repository\Financials\ReversalsRepository;
use App\Repository\Financials\WithdrawalsRepository;
use App\Repository\Merchant\PortalUserRepository;
use App\Repository\MerchantRepository;

use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnalyticsController extends AbstractController
{
    #[Route('/analytics', name: 'app_analytics')]
    public function index(
        MerchantRepository $merchantRepository,
        FinancialsRepository $salesRepository,
        CommissionEarnedRepository $commissionRepository,
        PortalUserRepository $userRepository,
        TransactionRepository $transactionRepository,
        WithdrawalsRepository $withdrawalsRepository,
        ReversalsRepository $reversalsRepository
    ): Response {
        // Get all analytics data
        $totalMerchants = $merchantRepository->count([]);
        $activeMerchants = $merchantRepository->count(['isActive' => true]);
        $inactiveMerchants = $merchantRepository->count(['isActive' => false]);

        $totalUsers = $userRepository->count(['enabled' => true]);

        // Get sales data for charts
        $monthlySales = $salesRepository->getMonthlySales();
        $merchantStatusData = [
            'active' => $activeMerchants,
            'inactive' => $inactiveMerchants,
            'trial' => 0, // You might need to add this field to your merchant table
            'suspended' => 0 // You might need to add this field to your merchant table
        ];

        // Get user activity data (last 7 days)
        $userActivity = $userRepository->getWeeklyActivity();

        // Get top performing merchants
        $topMerchants = $salesRepository->getTopPerformingMerchants();

        // Get recent merchant registrations
        $recentMerchants = $merchantRepository->findBy([], ['registrationDate' => 'DESC'], 5);

        // Get financial statistics
        $totalDeposits = $transactionRepository->getTotalDeposits();
        $totalWithdrawals = $withdrawalsRepository->getTotalWithdrawals();
        $totalReversals = $reversalsRepository->getTotalReversals();
        $totalCommission = $commissionRepository->getTotalCommission();

        return $this->render('superadmin/admin_analytics.html.twig', [
            'totalMerchants' => $totalMerchants,
            'activeMerchants' => $activeMerchants,
            'inactiveMerchants' => $inactiveMerchants,
            'totalUsers' => $totalUsers,
            'monthlySales' => $monthlySales,
            'merchantStatusData' => $merchantStatusData,
            'userActivity' => $userActivity,
            'topMerchants' => $topMerchants,
            'recentMerchants' => $recentMerchants,
            'totalDeposits' => $totalDeposits,
            'totalWithdrawals' => $totalWithdrawals,
            'totalReversals' => $totalReversals,
            'totalCommission' => $totalCommission,
        ]);
    }
}
