<?php

namespace App\Repository\Financials;

use App\Entity\Financials\CommissionEarned;
use App\Entity\Merchant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommissionEarnedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommissionEarned::class);
    }

    /**
     * Find commissions grouped by type and provider for a merchant
     */
    public function findGroupedByTypeAndProvider(int $merchantId): array
    {
        return $this->createQueryBuilder('ce')
            ->select(
                'ce.commissionType as Type',
                'ce.provider as Provider',
                'SUM(ce.totalSales) as Sales',
                'SUM(ce.merchantCommission) as Merchant',
                'ce.rate as Rate'
            )
            ->where('ce.merchant = :merchantId')
            ->setParameter('merchantId', $merchantId)
            ->groupBy('ce.commissionType', 'ce.provider', 'ce.rate')
            ->orderBy('ce.commissionType', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all earned commissions for a merchant with optional date range
     */
    public function findByMerchant(
        Merchant $merchant,
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null
    ): array {
        $qb = $this->createQueryBuilder('ce')
            ->where('ce.merchant = :merchant')
            ->setParameter('merchant', $merchant)
            ->orderBy('ce.saleDate', 'DESC');

        if ($startDate) {
            $qb->andWhere('ce.saleDate >= :startDate')
                ->setParameter('startDate', $startDate);
        }

        if ($endDate) {
            $qb->andWhere('ce.saleDate <= :endDate')
                ->setParameter('endDate', $endDate);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Get total earned commission for a merchant
     */
    public function getTotalEarnedCommission(int $merchantId): float
    {
        $result = $this->createQueryBuilder('ce')
            ->select('SUM(ce.merchantCommission) as total')
            ->where('ce.merchant = :merchantId')
            ->setParameter('merchantId', $merchantId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float)$result : 0.0;
    }

    /**
     * Save a commission earned record
     */
    public function save(CommissionEarned $commissionEarned, bool $flush = false): void
    {
        $this->getEntityManager()->persist($commissionEarned);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a commission earned record
     */
    public function remove(CommissionEarned $commissionEarned, bool $flush = false): void
    {
        $this->getEntityManager()->remove($commissionEarned);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getTotalCommission(): float
    {
        $query = $this->createQueryBuilder('ec')
            ->select('SUM(ec.merchantCommission) as total')
            ->getQuery();

        return $query->getSingleScalarResult() ?? 0;
    }
}
