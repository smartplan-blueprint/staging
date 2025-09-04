<?php

namespace App\Controller;

use App\Repository\MerchantRepository;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FinancialsController extends AbstractController
{
//    #[Route('/financials', name: 'app_financials')]
//    public function show_financials(Request $request,
//                                    MerchantRepository $merchantRepository,
//                                    TransactionRepository $transactionRepository): Response
//    {
//
//        // Get all transactions for grouping
//        $allTransactions = $transactionRepository->findAllWithMerchants();
//
//        // Group transactions by merchant ID and calculate totals
//        $groupedTransactions = [];
//        $metricTotals = [
//            'opening' => 0,
//            'deposits' => 0,
//            'sales' => 0,
//            'commission' => 0,
//            'reversals' => 0,
//            'closing' => 0,
//        ];
//
//        foreach ($allTransactions as $transaction) {
//            $merchant = $transaction->getMerchant();
//            $merchantId = $merchant->getId();
//
//            if (!isset($groupedTransactions[$merchantId])) {
//
//                $metricTotals['opening'] += $transaction->getOpeningBalance();
//                $metricTotals['deposits'] += $transaction->getDeposits();
//                $metricTotals['sales'] += $transaction->getSales();
//                $metricTotals['closing'] += $transaction->getClosingBalance();
//            }
//        }
//
//        // Get specific merchant data
//        $merchant = $merchantRepository->findOneBy(['code' => '3928']);
//        if (!$merchant) {
//            throw $this->createNotFoundException('Merchant not found');
//        }
//
//        $dateRange = $this->getDateRange($request);
//        $transactions = $transactionRepository->findByMerchantAndDateRange(
//            $merchant,
//            $dateRange['startDate'],
//            $dateRange['endDate']
//        );
//
//        $summary = $transactionRepository->getSummaryForMerchant(
//            $merchant,
//            $dateRange['startDate'],
//            $dateRange['endDate']
//        );
//
//        return $this->render('financials/financials.html.twig',
//        [
//            'merchant' => $merchant,
//            'transactions' => $transactions,
//            'summary' => $summary,
//            'dateRange' => $dateRange,
//            'groupedTransactions' => $groupedTransactions,
//            'metricTotals' => $metricTotals,
//        ]);
//    }
//
//    #[Route('/commission', name: 'financials_commission')]
//    public function commission(): Response
//    {
//        return $this->render('financials/commission.html.twig');
//    }

//    #[Route('/earned_commission', name: 'financials_earned_commission')]
//    public function earned_commission(): Response
//    {
//        return $this->render('financials/earned_commission.html.twig');
//    }

//    #[Route('/deposits', name: 'financials_deposits')]
//    public function deposits(): Response
//    {
//        return $this->render('financials/deposits.html.twig');
//    }

//    #[Route('/reversals', name: 'financials_reversals')]
//    public function reversals(): Response
//    {
//        return $this->render('financials/reversals.html.twig');
//    }

    #[Route('/withdrawals', name: 'financials_withdrawals')]
    public function withdrawals(): Response
    {
        return $this->render('financials/withdrawals.html.twig');
    }

    #[Route('/transfers', name: 'financials_transfers')]
    public function transfers(): Response
    {
        return $this->render('financials/transfers.html.twig');
    }

//    #[Route('/summary', name: 'financials_summary')]
//    public function summary(): Response
//    {
//        return $this->render('financials/summary.html.twig');
//    }



    private function getDateRange(Request $request): array
    {
        $session = $request->getSession();

        // Check if we have a stored range in session
        $storedRange = $session->get('date_range', 'today');

        // Get range from request or use stored one
        $range = $request->query->get('range', $storedRange);

        // Store the current range in session
        $session->set('date_range', $range);

        $today = new \DateTime();
        $startDate = clone $today;
        $endDate = clone $today;

        switch ($range) {
            case 'week':
                $startDate->modify('-7 days');
                break;
            case 'month':
                $startDate = new \DateTime('first day of this month');
                break;
            case 'today':
            default:
                // Default to today (no modification needed)
                break;
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'display' => $this->formatDateRange($startDate, $endDate),
            'currentRange' => $range
        ];
    }

    private function formatDateRange(\DateTimeInterface $start, \DateTimeInterface $end): string
    {
        if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
            return $start->format('F jS, Y');
        }

        return $start->format('F jS') . ' - ' . $end->format('F jS, Y');
    }

}

