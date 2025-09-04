<?php
//
//namespace App\Repository\Financials;
//
//use App\Entity\Financials\Reversals;
//use App\Entity\Merchant;
//use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
//use Doctrine\Persistence\ManagerRegistry;
//
///**
// * @extends ServiceEntityRepository<Reversals>
// */
//class ReversalsRepository extends ServiceEntityRepository
//{
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, Reversals::class);
//    }
//
////    public function findByMerchantAndDateRange(Merchant $merchant, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
////    {
////        return $this->createQueryBuilder('r')
////            ->andWhere('r.merchant = :merchant')
////            ->andWhere('r.reversalDate BETWEEN :startDate AND :endDate')
////            ->setParameter('merchant', $merchant)
////            ->setParameter('startDate', $startDate)
////            ->setParameter('endDate', $endDate)
////            ->orderBy('r.reversalDate', 'DESC')
////            ->getQuery()
////            ->getResult();
////    }
////
////    public function getTotalReversalsForMerchant(Merchant $merchant, \DateTimeInterface $startDate, \DateTimeInterface $endDate): float
////    {
////        $result = $this->createQueryBuilder('r')
////            ->select('SUM(r.amount) as total')
////            ->andWhere('r.merchant = :merchant')
////            ->andWhere('r.reversalDate BETWEEN :startDate AND :endDate')
////            ->setParameter('merchant', $merchant)
////            ->setParameter('startDate', $startDate)
////            ->setParameter('endDate', $endDate)
////            ->getQuery()
////            ->getSingleScalarResult();
////
////        return $result ? (float) $result : 0.0;
////    }
//
//    /**
//     * Find reversals for a specific merchant within a date range
//     */
//    public function findByMerchantAndDateRange(
//        Merchant $merchant,
//        \DateTimeInterface $startDate,
//        \DateTimeInterface $endDate,
//        array $orderBy = ['reversalDate' => 'DESC']
//    ): array {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.merchant = :merchant')
//            ->andWhere('r.reversalDate BETWEEN :startDate AND :endDate')
//            ->setParameter('merchant', $merchant)
//            ->setParameter('startDate', $startDate)
//            ->setParameter('endDate', $endDate)
//            ->orderBy('r.' . key($orderBy), current($orderBy))
//            ->getQuery()
//            ->getResult();
//    }
//
//    /**
//     * Get total reversal amount for a merchant within a date range
//     */
//    public function getTotalReversalsForMerchant(
//        Merchant $merchant,
//        \DateTimeInterface $startDate,
//        \DateTimeInterface $endDate
//    ): float {
//        $result = $this->createQueryBuilder('r')
//            ->select('SUM(r.amount) as total')
//            ->andWhere('r.merchant = :merchant')
//            ->andWhere('r.reversalDate BETWEEN :startDate AND :endDate')
//            ->setParameter('merchant', $merchant)
//            ->setParameter('startDate', $startDate)
//            ->setParameter('endDate', $endDate)
//            ->getQuery()
//            ->getSingleScalarResult();
//
//        return $result ? (float) $result : 0.0;
//    }
//
//    /**
//     * Find reversals by status for a merchant
//     */
//    public function findByStatus(
//        Merchant $merchant,
//        string $status,
//        int $limit = null
//    ): array {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.merchant = :merchant')
//            ->andWhere('r.status = :status')
//            ->setParameter('merchant', $merchant)
//            ->setParameter('status', $status)
//            ->orderBy('r.reversalDate', 'DESC')
//            ->setMaxResults($limit)
//            ->getQuery()
//            ->getResult();
//    }
//
//    /**
//     * Get reversal statistics for dashboard
//     */
//    public function getReversalStatistics(Merchant $merchant): array
//    {
//        $today = new \DateTime();
//        $weekStart = (clone $today)->modify('-7 days');
//        $monthStart = new \DateTime('first day of this month');
//
//        return [
//            'today' => $this->getTotalReversalsForMerchant($merchant, $today, $today),
//            'week' => $this->getTotalReversalsForMerchant($merchant, $weekStart, $today),
//            'month' => $this->getTotalReversalsForMerchant($merchant, $monthStart, $today),
//            'pending' => $this->createQueryBuilder('r')
//                ->select('COUNT(r.id)')
//                ->where('r.merchant = :merchant')
//                ->andWhere('r.status = :status')
//                ->setParameter('merchant', $merchant)
//                ->setParameter('status', 'pending')
//                ->getQuery()
//                ->getSingleScalarResult()
//        ];
//    }
//
//    /**
//     * Save a reversal entity
//     */
//    public function save(Reversals $reversal, bool $flush = true): void
//    {
//        $this->_em->persist($reversal);
//        if ($flush) {
//            $this->_em->flush();
//        }
//    }
//
//    /**
//     * Remove a reversal entity
//     */
//    public function remove(Reversals $reversal, bool $flush = true): void
//    {
//        $this->_em->remove($reversal);
//        if ($flush) {
//            $this->_em->flush();
//        }
//    }
//
//}


namespace App\Repository\Financials;

use App\Entity\Financials\Reversals;
use App\Entity\Merchant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reversals>
 */
class ReversalsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reversals::class);
    }

    public function findByMerchantAndDateRange(
        Merchant           $merchant,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        array              $orderBy = ['reversalDate' => 'DESC']
    ): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.merchant = :merchant')
            ->andWhere('r.reversalDate BETWEEN :startDate AND :endDate')
            ->setParameter('merchant', $merchant)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('r.' . key($orderBy), current($orderBy))
            ->getQuery()
            ->getResult();
    }

    public function getTotalReversalsForMerchant(
        Merchant           $merchant,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): float
    {
        $result = $this->createQueryBuilder('r')
            ->select('SUM(r.amount) as total')
            ->andWhere('r.merchant = :merchant')
            ->andWhere('r.reversalDate BETWEEN :startDate AND :endDate')
            ->setParameter('merchant', $merchant)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float)$result : 0.0;
    }

    public function save(Reversals $reversal, bool $flush = true): void
    {
        $this->_em->persist($reversal);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Reversals $reversal, bool $flush = true): void
    {
        $this->_em->remove($reversal);
        if ($flush) {
            $this->_em->flush();
        }
    }
    public function getTotalReversals(): float
    {
        $query = $this->createQueryBuilder('r')
            ->select('SUM(r.amount) as total')
            ->where('r.status = :status')
            ->setParameter('status', 'completed')
            ->getQuery();

        return $query->getSingleScalarResult() ?? 0;
    }

}
