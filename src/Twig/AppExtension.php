<?php

// src/Twig/AppExtension.php
namespace App\Twig;

use App\Service\MerchantContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
//    public function getFunctions()
//    {
//        return [
//            new TwigFunction('get_report_route', [$this, 'getReportRoute']),
//        ];
//    }

    public function getReportRoute(string $reportName): string
    {
        $routes = [
            'Transactions' => 'app_transactions',
            'Itemised Report' => 'app_itemised_report',
            'Sales By Category' => 'app_category_report',
            'Sales By User' => 'app_user_report',
            'Sales By Outlet' => 'app_outlet_report',
            'Sales By Date' => 'app_sales_by_date_report',
//            'Recon Report' => 'app_recon_report',
//            'Merchant Statement' => 'app_merchant_statement',
            // Add more mappings as needed
            'Group Itemised Report' => 'group_itemised_report',
            'Group Category Report' => 'group_category_report',
            'Group Sales By Date' => 'group_sales_by_date_report'

        ];

        return $routes[$reportName] ?? 'app_reports';
    }

    public function getFilters()
    {
        return [
            new TwigFilter('format_percent', [$this, 'formatPercent']),
            new TwigFilter('format_bwp', [$this, 'formatCurrency']),
        ];
    }

    public function formatCurrency($amount): string
    {
        if (null === $amount) {
            return 'N/A';
        }

        return 'P' . number_format((float) $amount, 2, '.', ',');
    }

//    public function formatPercent(float $value): string
//    {
//        // Format as percentage (e.g., 0.09 → "9%")
//        return round($value * 100, 2) . '%';
//    }

    public function formatPercent($value): string
    {
        // Convert to float if it's a string
        $numericValue = is_numeric($value) ? (float)$value : 0.0;

        // Detect if value is likely stored as decimal (0.09) or whole number (9)
        if ($numericValue < 1) {
            // Value is in decimal format (0.09 → 9%)
            $percentage = round($numericValue * 100, 2);
        } else {
            // Value is already in percentage format (9 → 9%)
            $percentage = round($numericValue, 2);
        }

        // Format with 2 decimal places and % sign
        return number_format($percentage, 2) . '%';
    }

    public function formatBWP($value): string
    {
        $numericValue = is_numeric($value) ? (float)$value : 0.0;
        return 'P' . number_format($numericValue, 2);
    }

//    public function formatBWP(float $value): string
//    {
//        // Format as currency (e.g., 1000 → "P1,000.00")
//        return 'P' . number_format($value, 2);
//    }


    private $merchantContext;

    public function __construct(MerchantContext $merchantContext)
    {
        $this->merchantContext = $merchantContext;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_current_merchant', [$this, 'getCurrentMerchant']),
        ];
    }

    public function getCurrentMerchant()
    {
        return $this->merchantContext->getCurrentMerchant();
    }
}
