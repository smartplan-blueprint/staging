<?php

namespace App\Repository\Financials;


use App\Entity\Financials\Sales;
use App\Entity\Merchant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sales>
 */
class FinancialsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sales::class);
    }

    public function findGroupedByProvider(int $merchantId): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.saleType', 's.provider', 'SUM(s.total) as total')
            ->where('s.merchant = :merchantId')
            ->setParameter('merchantId', $merchantId)
            ->groupBy('s.saleType', 's.provider')
            ->orderBy('s.saleType', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalByTypeForMerchant(Merchant $merchant, string $type, \DateTimeInterface $start, \DateTimeInterface $end): string
    {
        $result = $this->createQueryBuilder('s')
            ->select('SUM(s.total)')
            ->where('s.merchant = :merchant')
            ->andWhere('s.saleType = :type')
            ->andWhere('s.saleDate BETWEEN :start AND :end')
            ->setParameter('merchant', $merchant)
            ->setParameter('type', $type)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function findDepositsByMerchant(int $merchantId): float
    {
        $result = $this->createQueryBuilder('s')
            ->select('SUM(s.depositAmount) as total')
            ->where('s.merchant = :merchantId')
            ->setParameter('merchantId', $merchantId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float)$result : 0.0;
    }

    public function findBySaleType(string $saleType): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.saleType = :saleType')
            ->setParameter('saleType', $saleType)
            ->orderBy('s.saleDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByProvider(string $provider): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.provider = :provider')
            ->setParameter('provider', $provider)
            ->orderBy('s.saleDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findRecentSales(int $limit = 10): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.saleDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function save(Sales $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sales $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    public function getMonthlySales(): array
//    {
//        $query = $this->createQueryBuilder('s')
//            ->select("MONTH(s.saleDate) as month, YEAR(s.saleDate) as year, SUM(s.total) as total")
//            ->groupBy('year, month')
//            ->orderBy('year', 'ASC')
//            ->addOrderBy('month', 'ASC')
//            ->getQuery();
//
//        $results = $query->getResult();
//
//        // Format the results with month names
//        $monthlyData = [];
//        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
//
//        foreach ($results as $result) {
//            $monthIndex = $result['month'] - 1; // Convert to 0-based index
//            if (isset($monthNames[$monthIndex])) {
//                $monthlyData[$monthNames[$monthIndex]] = (float) $result['total'];
//            }
//        }
//
//        // Ensure all months are present with 0 values
//        $completeData = [];
//        foreach ($monthNames as $month) {
//            $completeData[$month] = $monthlyData[$month] ?? 0;
//        }
//
//        return $completeData;
//    }

    public function getMonthlySales(): array
    {
        // Get sales data grouped by month
        $query = $this->createQueryBuilder('s')
            ->select('s.saleDate, SUM(s.total) as totalSales')
            ->groupBy('s.saleDate')
            ->orderBy('s.saleDate', 'ASC')
            ->getQuery();

        $results = $query->getResult();

        // Initialize monthly data with zeros
        $monthlyData = [
            'Jan' => 0, 'Feb' => 0, 'Mar' => 0, 'Apr' => 0,
            'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0,
            'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0
        ];

        // Process the results
        foreach ($results as $result) {
            if ($result['saleDate'] instanceof \DateTimeInterface) {
                $month = $result['saleDate']->format('M');
                $monthlyData[$month] += (float) $result['totalSales'];
            }
        }

        return $monthlyData;
    }

//    public function getMonthlySales(): array
//    {
//        $query = $this->createQueryBuilder('s')
//            ->select('s.saleDate, s.total')
//            ->orderBy('s.saleDate', 'ASC')
//            ->getQuery();
//
//        $results = $query->getResult();
//
//        // Process data in PHP
//        $monthlyData = array_fill_keys(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], 0);
//
//        foreach ($results as $result) {
//            if ($result['saleDate'] instanceof \DateTime) {
//                $month = $result['saleDate']->format('M');
//                $monthlyData[$month] += (float) $result['total'];
//            }
//        }
//
//        return $monthlyData;
//    }
    public function getTopPerformingMerchants(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('m.name as merchantName, SUM(s.total) as totalSales')
            ->join('s.merchant', 'm')
            ->groupBy('s.merchant')
            ->orderBy('totalSales', 'DESC')
            ->setMaxResults(5)
            ->getQuery();

        return $query->getResult();
    }
}
