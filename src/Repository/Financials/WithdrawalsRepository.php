<?php

namespace App\Repository\Financials;

use App\Entity\Financials\Withdrawal;
use App\Entity\Merchant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class WithdrawalsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Withdrawal::class); // Update with your actual entity class
    }

    public function getTotalForMerchant(
        Merchant $merchant,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): string {
        $result = $this->createQueryBuilder('w')
            ->select('SUM(w.amount) as total') // Use your actual amount field name
            ->where('w.merchant = :merchant')
            ->andWhere('w.createdAt BETWEEN :start AND :end') // Use your actual date field name
            ->setParameter('merchant', $merchant)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (string) $result : '0';
    }

    public function getTotalWithdrawals(): float
    {
        $query = $this->createQueryBuilder('w')
            ->select('SUM(w.amount) as total')
            ->where('w.status = :status')
            ->setParameter('status', 'completed')
            ->getQuery();

        return (float) ($query->getSingleScalarResult() ?? 0);
    }

}
