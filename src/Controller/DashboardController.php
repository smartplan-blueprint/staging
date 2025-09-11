<?php


//namespace App\Controller;
//
//use App\Entity\Merchant;
//use App\Repository\MerchantRepository;
//use App\Repository\TransactionRepository;
//use App\Service\MerchantContext;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;
//
//#[Route('/dashboard', name: 'app_dashboard')]
//class DashboardController extends AbstractController
//{
//    private TransactionRepository $transactionRepository;
//    private MerchantContext $merchantContext;
//
//    public function __construct(
//        TransactionRepository $transactionRepository,
//        MerchantContext       $merchantContext
//    )
//    {
//        $this->transactionRepository = $transactionRepository;
//        $this->merchantContext = $merchantContext;
//    }
//
//    #[Route('/', name: 'app_home')]
//    public function home(): Response
//    {
//        return $this->redirectToRoute('app_dashboard');
//    }
//
//    #[Route('/', name: 'app_dashboard')]
//    public function index(
//        Request            $request,
//        MerchantRepository $merchantRepository,
//        ?int               $id = null
//    ): Response
//    {
//
//        // Get current merchant from context or parameter
//        $currentMerchant = $this->getCurrentMerchant($merchantRepository, $id);
//
//        if (!$currentMerchant) {
//            // If no merchant is selected, redirect to merchant selection
//            return $this->redirectToRoute('app_merchant_selection');
//        }
//
//        // Store the current merchant in session
//        $this->merchantContext->setCurrentMerchant($currentMerchant);
//
//        // Get all transactions for grouping
//        $allTransactions = $this->transactionRepository->findAllWithMerchants();
//
//        // Group transactions by merchant ID and calculate totals
//        $groupedTransactions = [];
//        $grandTotals = [
//            'opening' => 0,
//            'deposits' => 0,
//            'sales' => 0,
//            'closing' => 0
//        ];
//
//        $metricTotals = [
//            'opening' => 0,
//            'deposits' => 0,
//            'sales' => 0,
//            'closing' => 0
//        ];
//
//        foreach ($allTransactions as $transaction) {
//            $merchant = $transaction->getMerchant();
//            $merchantId = $merchant->getId();
//
//            if (!isset($groupedTransactions[$merchantId])) {
//                $groupedTransactions[$merchantId] = [
//                    'merchant' => $merchant,
//                    'transactions' => [],
//                    'totals' => [
//                        'opening' => 0,
//                        'deposits' => 0,
//                        'sales' => 0,
//                        'closing' => 0
//                    ]
//                ];
//            }
//
//            $groupedTransactions[$merchantId]['transactions'][] = $transaction;
//            $groupedTransactions[$merchantId]['totals']['opening'] += $transaction->getOpeningBalance();
//            $groupedTransactions[$merchantId]['totals']['deposits'] += $transaction->getDeposits();
//            $groupedTransactions[$merchantId]['totals']['sales'] += $transaction->getSales();
//            $groupedTransactions[$merchantId]['totals']['closing'] += $transaction->getClosingBalance();
//
//            // Add to grand totals
//            $grandTotals['opening'] += $transaction->getOpeningBalance();
//            $grandTotals['deposits'] += $transaction->getDeposits();
//            $grandTotals['sales'] += $transaction->getSales();
//            $grandTotals['closing'] += $transaction->getClosingBalance();
//
//            $metricTotals['opening'] += $transaction->getOpeningBalance();
//            $metricTotals['deposits'] += $transaction->getDeposits();
//            $metricTotals['sales'] += $transaction->getSales();
//            $metricTotals['closing'] += $transaction->getClosingBalance();
//        }
//
//        // Get transactions for the current merchant
//        $dateRange = $this->getDateRange($request);
//        $transactions = $this->transactionRepository->findByMerchantAndDateRange(
//            $currentMerchant,
//            $dateRange['startDate'],
//            $dateRange['endDate']
//        );
//
//        $summary = $this->transactionRepository->getSummaryForMerchant(
//            $currentMerchant,
//            $dateRange['startDate'],
//            $dateRange['endDate']
//        );
//
//        return $this->render('dashboard/index.html.twig', [
//            'merchant' => $currentMerchant,
//            'transactions' => $transactions,
//            'summary' => $summary,
//            'dateRange' => $dateRange,
//            'groupedTransactions' => $groupedTransactions,
//            'grandTotals' => $grandTotals,
//            'metricTotals' => $metricTotals,
//            'allMerchants' => $merchantRepository->findAll() // For merchant switcher
//        ]);
//    }
//
//    private function getCurrentMerchant(MerchantRepository $merchantRepository, ?int $id): ?Merchant
//    {
//        // Priority 1: ID from URL parameter
//        if ($id) {
//            return $merchantRepository->find($id);
//        }
//
//        // Priority 2: Merchant from context service
//        $currentMerchant = $this->merchantContext->getCurrentMerchant();
//        if ($currentMerchant) {
//            return $currentMerchant;
//        }
//
//        // Priority 3: Default merchant (code '3928')
//        return $merchantRepository->findOneBy(['code' => '3928']);
//    }
//
//    private function getDateRange(Request $request): array
//    {
//        $session = $request->getSession();
//
//        // Check if we have a stored range in session
//        $storedRange = $session->get('date_range', 'today');
//
//        // Get range from request or use stored one
//        $range = $request->query->get('range', $storedRange);
//
//        // Store the current range in session
//        $session->set('date_range', $range);
//
//        $today = new \DateTime();
//        $startDate = clone $today;
//        $endDate = clone $today;
//
//        switch ($range) {
//            case 'week':
//                $startDate->modify('-7 days');
//                break;
//            case 'month':
//                $startDate = new \DateTime('first day of this month');
//                break;
//            case 'today':
//            default:
//                // Default to today (no modification needed)
//                break;
//        }
//
//        return [
//            'startDate' => $startDate,
//            'endDate' => $endDate,
//            'display' => $this->formatDateRange($startDate, $endDate),
//            'currentRange' => $range
//        ];
//    }
//
//    private function formatDateRange(\DateTimeInterface $start, \DateTimeInterface $end): string
//    {
//        if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
//            return $start->format('F jS, Y');
//        }
//
//        return $start->format('F jS') . ' - ' . $end->format('F jS, Y');
//    }
//
//    #[Route('/select-merchant/{id}', name: 'merchant_selection')]
//    public function selectMerchant(int $id, MerchantRepository $merchantRepository): Response
//    {
//        $merchant = $merchantRepository->find($id);
//        if (!$merchant) {
//            throw $this->createNotFoundException('Merchant not found');
//        }
//
//        $this->merchantContext->setCurrentMerchant($merchant);
//
//        return $this->redirectToRoute('app_dashboard', ['id' => $id]);
//    }
//}


// src/Controller/DashboardController.php
// src/Controller/DashboardController.php

namespace App\Controller;

use App\Entity\Merchant;
use App\Repository\MerchantRepository;
use App\Repository\TransactionRepository;
use App\Service\MerchantContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private TransactionRepository $transactionRepository;
    private MerchantContext $merchantContext;

    public function __construct(
        TransactionRepository $transactionRepository,
        MerchantContext       $merchantContext
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->merchantContext = $merchantContext;
    }

    #[Route('/dashboard', name: 'app_dashboard_home')]
    public function home(): Response
    {
        // Redirect based on user role
        if ($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_GROUP_ADMIN')) {
            return $this->redirectToRoute('app_dashboard_admin');
        } else {
            // For regular merchants, redirect to their dashboard
            $merchant = $this->merchantContext->getCurrentMerchant();
            if ($merchant) {
                return $this->redirectToRoute('app_dashboard_merchant', ['id' => $merchant->getId()]);
            }
            return $this->redirectToRoute('app_merchant_selection');
        }
    }

    #[Route('/dashboard/admin', name: 'app_dashboard_admin')]
    public function adminDashboard(
        Request            $request,
        MerchantRepository $merchantRepository
    ): Response
    {
        // Only allow super admins and group admins to access this dashboard
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
      //  $this->denyAccessUnlessGranted('ROLE_GROUP_ADMIN', null, 'Access denied. You need group admin privileges.');

        // Get all merchants for the admin view
        $merchants = $merchantRepository->findAll();

        // Get date range for filtering
        $dateRange = $this->getDateRange($request);

        // Get all transactions for grouping
        $allTransactions = $this->transactionRepository->findAllWithMerchants();

        // Group transactions by merchant ID and calculate totals
        $groupedTransactions = [];
        $grandTotals = [
            'opening' => 0,
            'deposits' => 0,
            'sales' => 0,
            'closing' => 0
        ];

        foreach ($allTransactions as $transaction) {
            $merchant = $transaction->getMerchant();
            $merchantId = $merchant->getId();

            if (!isset($groupedTransactions[$merchantId])) {
                $groupedTransactions[$merchantId] = [
                    'merchant' => $merchant,
                    'transactions' => [],
                    'totals' => [
                        'opening' => 0,
                        'deposits' => 0,
                        'sales' => 0,
                        'closing' => 0
                    ]
                ];
            }

            $groupedTransactions[$merchantId]['transactions'][] = $transaction;
            $groupedTransactions[$merchantId]['totals']['opening'] += $transaction->getOpeningBalance();
            $groupedTransactions[$merchantId]['totals']['deposits'] += $transaction->getDeposits();
            $groupedTransactions[$merchantId]['totals']['sales'] += $transaction->getSales();
            $groupedTransactions[$merchantId]['totals']['closing'] += $transaction->getClosingBalance();

            // Add to grand totals
            $grandTotals['opening'] += $transaction->getOpeningBalance();
            $grandTotals['deposits'] += $transaction->getDeposits();
            $grandTotals['sales'] += $transaction->getSales();
            $grandTotals['closing'] += $transaction->getClosingBalance();
        }

        return $this->render('superadmin/admin_index.html.twig', [
            'merchants' => $merchants,
            'groupedTransactions' => $groupedTransactions,
            'grandTotals' => $grandTotals,
            'dateRange' => $dateRange,
            'is_admin_view' => true
        ]);
    }

    #[Route('/dashboard/{id}', name: 'app_dashboard_merchant')]
    public function merchantDashboard(
        Request $request,
        MerchantRepository $merchantRepository,
        ?int $id = null
    ): Response
    {
        // Get current merchant from context or parameter
        $currentMerchant = $this->getCurrentMerchant($merchantRepository, $id);

        if (!$currentMerchant) {
            // If no merchant is selected, redirect to merchant selection
            return $this->redirectToRoute('app_merchant_selection');
        }

        // Store the current merchant in session
        $this->merchantContext->setCurrentMerchant($currentMerchant);

        // Get date range
        $dateRange = $this->getDateRange($request);

        // Get transactions for the current merchant
        $transactions = $this->transactionRepository->findByMerchantAndDateRange(
            $currentMerchant,
            $dateRange['startDate'],
            $dateRange['endDate']
        );

        $summary = $this->transactionRepository->getSummaryForMerchant(
            $currentMerchant,
            $dateRange['startDate'],
            $dateRange['endDate']
        );

        // Calculate metric totals for current merchant
        $metricTotals = $this->calculateMetricTotals($transactions);

        // For single merchant view, create groupedTransactions with just the current merchant
        $groupedTransactions = [
            $currentMerchant->getId() => [
                'merchant' => $currentMerchant,
                'totals' => $metricTotals
            ]
        ];

        return $this->render('dashboard/index.html.twig', [
            'merchant' => $currentMerchant,
            'transactions' => $transactions,
            'summary' => $summary,
            'dateRange' => $dateRange,
            'groupedTransactions' => $groupedTransactions,
            'metricTotals' => $metricTotals,
            'allMerchants' => $merchantRepository->findAll(), // For merchant switcher
            'is_admin_view' => false
        ]);
    }

    private function calculateMetricTotals(array $transactions): array
    {
        // If no transactions, return zeros
        if (empty($transactions)) {
            return [
                'opening' => 0,
                'deposits' => 0,
                'sales' => 0,
                'closing' => 0
            ];
        }

        $opening = 0;
        $deposits = 0;
        $sales = 0;
        $closing = 0;

        foreach ($transactions as $transaction) {
            $opening += $transaction->getOpeningBalance();
            $deposits += $transaction->getDeposits();
            $sales += $transaction->getSales();
            $closing += $transaction->getClosingBalance();
        }

        return [
            'opening' => $opening,
            'deposits' => $deposits,
            'sales' => $sales,
            'closing' => $closing
        ];
    }

    private function getDateRange(Request $request): array
    {
        $range = $request->query->get('range', 'today');
        $now = new \DateTime();

        switch ($range) {
            case 'week':
                $startDate = (clone $now)->modify('-7 days');
                $display = $startDate->format('M j') . ' - ' . $now->format('M j, Y');
                break;
            case 'month':
                $startDate = new \DateTime('first day of this month');
                $display = $startDate->format('M j') . ' - ' . $now->format('M j, Y');
                break;
            case 'today':
            default:
                $startDate = (clone $now)->setTime(0, 0, 0);
                $display = $now->format('M j, Y');
                break;
        }

        return [
            'startDate' => $startDate,
            'endDate' => $now,
            'display' => $display,
            'currentRange' => $range
        ];
    }

    private function getCurrentMerchant(MerchantRepository $merchantRepository, ?int $id): ?Merchant
    {
        // Priority 1: ID from URL parameter
        if ($id) {
            return $merchantRepository->find($id);
        }

        // Priority 2: Merchant from context service
        $currentMerchant = $this->merchantContext->getCurrentMerchant();
        if ($currentMerchant) {
            return $currentMerchant;
        }

        // Priority 3: Default merchant (code '3928')
        return $merchantRepository->findOneBy(['code' => '3928']);
    }

    private function formatDateRange(\DateTimeInterface $start, \DateTimeInterface $end): string
    {
        if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
            return $start->format('F jS, Y');
        }

        return $start->format('F jS') . ' - ' . $end->format('F jS, Y');
    }

    #[Route('/dashboard/select-merchant/{id}', name: 'app_merchant_selection', defaults: [null])]
    public function selectMerchant(int $id, MerchantRepository $merchantRepository): Response
    {
        $merchant = $merchantRepository->find($id);
        if (!$merchant) {
            throw $this->createNotFoundException('Merchant not found');
        }

        $this->merchantContext->setCurrentMerchant($merchant);

        return $this->redirectToRoute('app_dashboard_merchant', ['id' => $id]);
    }
}

//namespace App\Controller;
//
//use App\Entity\Merchant;
//use App\Repository\MerchantRepository;
//use App\Repository\TransactionRepository;
//use App\Service\MerchantContext;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;
//
//#[Route('/dashboard', name: 'app_dashboard')]
//class DashboardController extends AbstractController
//{
//    private TransactionRepository $transactionRepository;
//    private MerchantContext $merchantContext;
//
//    public function __construct(
//        TransactionRepository $transactionRepository,
//        MerchantContext       $merchantContext
//    )
//    {
//        $this->transactionRepository = $transactionRepository;
//        $this->merchantContext = $merchantContext;
//    }
//
//    #[Route('/', name: 'home')]
//    public function home(): Response
//    {
//        return $this->redirectToRoute('app_dashboard');
//    }
//
//    #[Route('/admin', name: 'admin')]
//    public function adminDashboard(
//        Request            $request,
//        MerchantRepository $merchantRepository
//    ): Response
//    {
//        // Only allow super admins and admins to access this dashboard
//        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
//
//        // Get all merchants for the admin view
//        $merchants = $merchantRepository->findAll();
//
//        // Get date range for filtering
//        $dateRange = $this->getDateRange($request);
//
//        // Get all transactions for grouping
//        $allTransactions = $this->transactionRepository->findAllWithMerchants();
//
//        // Group transactions by merchant ID and calculate totals
//        $groupedTransactions = [];
//        $grandTotals = [
//            'opening' => 0,
//            'deposits' => 0,
//            'sales' => 0,
//            'closing' => 0
//        ];
//
//        foreach ($allTransactions as $transaction) {
//            $merchant = $transaction->getMerchant();
//            $merchantId = $merchant->getId();
//
//            if (!isset($groupedTransactions[$merchantId])) {
//                $groupedTransactions[$merchantId] = [
//                    'merchant' => $merchant,
//                    'transactions' => [],
//                    'totals' => [
//                        'opening' => 0,
//                        'deposits' => 0,
//                        'sales' => 0,
//                        'closing' => 0
//                    ]
//                ];
//            }
//
//            $groupedTransactions[$merchantId]['transactions'][] = $transaction;
//            $groupedTransactions[$merchantId]['totals']['opening'] += $transaction->getOpeningBalance();
//            $groupedTransactions[$merchantId]['totals']['deposits'] += $transaction->getDeposits();
//            $groupedTransactions[$merchantId]['totals']['sales'] += $transaction->getSales();
//            $groupedTransactions[$merchantId]['totals']['closing'] += $transaction->getClosingBalance();
//
//            // Add to grand totals
//            $grandTotals['opening'] += $transaction->getOpeningBalance();
//            $grandTotals['deposits'] += $transaction->getDeposits();
//            $grandTotals['sales'] += $transaction->getSales();
//            $grandTotals['closing'] += $transaction->getClosingBalance();
//        }
//
//        return $this->render('dashboard/index.html.twig', [
//            'merchants' => $merchants,
//            'groupedTransactions' => $groupedTransactions,
//            'grandTotals' => $grandTotals,
//            'dateRange' => $dateRange,
//            'is_admin_view' => true
//        ]);
//    }
//
////    #[Route('/merchant', name: 'app_dashboard')]
////    public function index(
////        Request            $request,
////        MerchantRepository $merchantRepository,
////        TransactionRepository $transactionRepository,
////        MerchantContext       $merchantContext,
////        ?int               $id = null
////    ): Response
////    {
////        // Get current merchant from context or parameter
////        $currentMerchant = $this->getCurrentMerchant($merchantRepository, $id);
////
////        if (!$currentMerchant) {
////            // If no merchant is selected, redirect to merchant selection
////            return $this->redirectToRoute('app_merchant_selection');
////        }
////
////        // Store the current merchant in session
////        $this->merchantContext->setCurrentMerchant($currentMerchant);
////
////        // Get all transactions for grouping
////        $allTransactions = $this->transactionRepository->findAllWithMerchants();
////
////        // Group transactions by merchant ID and calculate totals
////        $groupedTransactions = [];
////        $grandTotals = [
////            'opening' => 0,
////            'deposits' => 0,
////            'sales' => 0,
////            'closing' => 0
////        ];
////
////        $metricTotals = [
////            'opening' => 0,
////            'deposits' => 0,
////            'sales' => 0,
////            'closing' => 0
////        ];
////
////        foreach ($allTransactions as $transaction) {
////            $merchant = $transaction->getMerchant();
////            $merchantId = $merchant->getId();
////
////            if (!isset($groupedTransactions[$merchantId])) {
////                $groupedTransactions[$merchantId] = [
////                    'merchant' => $merchant,
////                    'transactions' => [],
////                    'totals' => [
////                        'opening' => 0,
////                        'deposits' => 0,
////                        'sales' => 0,
////                        'closing' => 0
////                    ]
////                ];
////            }
////
////            $groupedTransactions[$merchantId]['transactions'][] = $transaction;
////            $groupedTransactions[$merchantId]['totals']['opening'] += $transaction->getOpeningBalance();
////            $groupedTransactions[$merchantId]['totals']['deposits'] += $transaction->getDeposits();
////            $groupedTransactions[$merchantId]['totals']['sales'] += $transaction->getSales();
////            $groupedTransactions[$merchantId]['totals']['closing'] += $transaction->getClosingBalance();
////
////            // Add to grand totals
////            $grandTotals['opening'] += $transaction->getOpeningBalance();
////            $grandTotals['deposits'] += $transaction->getDeposits();
////            $grandTotals['sales'] += $transaction->getSales();
////            $grandTotals['closing'] += $transaction->getClosingBalance();
////
////            $metricTotals['opening'] += $transaction->getOpeningBalance();
////            $metricTotals['deposits'] += $transaction->getDeposits();
////            $metricTotals['sales'] += $transaction->getSales();
////            $metricTotals['closing'] += $transaction->getClosingBalance();
////        }
////
////        // Get transactions for the current merchant
////        $dateRange = $this->getDateRange($request);
////        $transactions = $this->transactionRepository->findByMerchantAndDateRange(
////            $currentMerchant,
////            $dateRange['startDate'],
////            $dateRange['endDate']
////        );
////
////        $summary = $this->transactionRepository->getSummaryForMerchant(
////            $currentMerchant,
////            $dateRange['startDate'],
////            $dateRange['endDate']
////        );
////
////        return $this->render('dashboard/index.html.twig', [
////            'merchant' => $currentMerchant,
////            'transactions' => $transactions,
////            'summary' => $summary,
////            'dateRange' => $dateRange,
////            'groupedTransactions' => $groupedTransactions,
////            'grandTotals' => $grandTotals,
////            'metricTotals' => $metricTotals,
////            'allMerchants' => $merchantRepository->findAll(), // For merchant switcher
////            'is_admin_view' => false
////        ]);
////    }
//
//    #[Route('/{id}', name: 'app_dashboard')]
//    public function index(
//        Request $request,
//        MerchantRepository $merchantRepository,
//        TransactionRepository $transactionRepository,
//        MerchantContext $merchantContext,
//        ?int $id = null
//    ): Response
//    {
//        // Get current merchant from context or parameter
//        $currentMerchant = $this->getCurrentMerchant($merchantRepository, $id);
//
//        if (!$currentMerchant) {
//            // If no merchant is selected, redirect to merchant selection
//            return $this->redirectToRoute('app_merchant_selection');
//        }
//
//        // Store the current merchant in session
//        $this->merchantContext->setCurrentMerchant($currentMerchant);
//
//        // Get date range
//        $dateRange = $this->getDateRange($request);
//
//        // Get transactions for the current merchant
//        $transactions = $this->transactionRepository->findByMerchantAndDateRange(
//            $currentMerchant,
//            $dateRange['startDate'],
//            $dateRange['endDate']
//        );
//
//        $summary = $this->transactionRepository->getSummaryForMerchant(
//            $currentMerchant,
//            $dateRange['startDate'],
//            $dateRange['endDate']
//        );
//
//        // Calculate metric totals for current merchant
//        $metricTotals = $this->calculateMetricTotals($transactions);
//
//        // For single merchant view, create groupedTransactions with just the current merchant
//        $groupedTransactions = [
//            $currentMerchant->getId() => [
//                'merchant' => $currentMerchant,
//                'totals' => $metricTotals
//            ]
//        ];
//
//        return $this->render('dashboard/index.html.twig', [
//            'merchant' => $currentMerchant,
//            'transactions' => $transactions,
//            'summary' => $summary,
//            'dateRange' => $dateRange,
//            'groupedTransactions' => $groupedTransactions,
//            'metricTotals' => $metricTotals,
//            'allMerchants' => $merchantRepository->findAll(), // For merchant switcher
//            'is_admin_view' => false
//        ]);
//    }
//
//    private function calculateMetricTotals(array $transactions): array
//    {
//        // If no transactions, return zeros
//        if (empty($transactions)) {
//            return [
//                'opening' => 0,
//                'deposits' => 0,
//                'sales' => 0,
//                'closing' => 0
//            ];
//        }
//
//        $opening = 0;
//        $deposits = 0;
//        $sales = 0;
//        $closing = 0;
//
//        foreach ($transactions as $transaction) {
//            $opening += $transaction->getOpeningBalance();
//            $deposits += $transaction->getDeposits();
//            $sales += $transaction->getSales();
//            $closing += $transaction->getClosingBalance();
//        }
//
//        return [
//            'opening' => $opening,
//            'deposits' => $deposits,
//            'sales' => $sales,
//            'closing' => $closing
//        ];
//    }
//
//    private function getDateRange(Request $request): array
//    {
//        $range = $request->query->get('range', 'today');
//        $now = new \DateTime();
//
//        switch ($range) {
//            case 'week':
//                $startDate = (clone $now)->modify('-7 days');
//                $display = $startDate->format('M j') . ' - ' . $now->format('M j, Y');
//                break;
//            case 'month':
//                $startDate = new \DateTime('first day of this month');
//                $display = $startDate->format('M j') . ' - ' . $now->format('M j, Y');
//                break;
//            case 'today':
//            default:
//                $startDate = (clone $now)->setTime(0, 0, 0);
//                $display = $now->format('M j, Y');
//                break;
//        }
//
//        return [
//            'startDate' => $startDate,
//            'endDate' => $now,
//            'display' => $display,
//            'currentRange' => $range
//        ];
//    }
//    private function getCurrentMerchant(MerchantRepository $merchantRepository, ?int $id): ?Merchant
//    {
//        // Priority 1: ID from URL parameter
//        if ($id) {
//            return $merchantRepository->find($id);
//        }
//
//        // Priority 2: Merchant from context service
//        $currentMerchant = $this->merchantContext->getCurrentMerchant();
//        if ($currentMerchant) {
//            return $currentMerchant;
//        }
//
//        // Priority 3: Default merchant (code '3928')
//        return $merchantRepository->findOneBy(['code' => '3928']);
//    }
//
////    private function getDateRange(Request $request): array
////    {
////        $session = $request->getSession();
////
////        // Check if we have a stored range in session
////        $storedRange = $session->get('date_range', 'today');
////
////        // Get range from request or use stored one
////        $range = $request->query->get('range', $storedRange);
////
////        // Store the current range in session
////        $session->set('date_range', $range);
////
////        $today = new \DateTime();
////        $startDate = clone $today;
////        $endDate = clone $today;
////
////        switch ($range) {
////            case 'week':
////                $startDate->modify('-7 days');
////                break;
////            case 'month':
////                $startDate = new \DateTime('first day of this month');
////                break;
////            case 'today':
////            default:
////                // Default to today (no modification needed)
////                break;
////        }
////
////        return [
////            'startDate' => $startDate,
////            'endDate' => $endDate,
////            'display' => $this->formatDateRange($startDate, $endDate),
////            'currentRange' => $range
////        ];
////    }
//
//    private function formatDateRange(\DateTimeInterface $start, \DateTimeInterface $end): string
//    {
//        if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
//            return $start->format('F jS, Y');
//        }
//
//        return $start->format('F jS') . ' - ' . $end->format('F jS, Y');
//    }
//
//    #[Route('/select-merchant/{id}', name: 'merchant_selection')]
//    public function selectMerchant(int $id, MerchantRepository $merchantRepository): Response
//    {
//        $merchant = $merchantRepository->find($id);
//        if (!$merchant) {
//            throw $this->createNotFoundException('Merchant not found');
//        }
//
//        $this->merchantContext->setCurrentMerchant($merchant);
//
//        return $this->redirectToRoute('app_dashboard_index', ['id' => $id]);
//    }
//}
