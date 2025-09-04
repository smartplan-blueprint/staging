<?php
//// src/Repository/TransactionRepository.php
//namespace App\Repository;
//
//use App\Entity\Merchant;
//use App\Entity\Transaction;
//use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
//use Doctrine\Persistence\ManagerRegistry;
//
//class TransactionRepository extends ServiceEntityRepository
//{
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, Transaction::class);
//    }
//
//    public function findByDateAndRange(Merchant $merchant, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.merchant = :merchant')
//            ->andWhere('t.date BETWEEN :start AND :end')
//            ->setParameter('merchant', $merchant)
//            ->setParameter('start', $startDate->format('Y-m-d 00:00:00'))
//            ->setParameter('end', $endDate->format('Y-m-d 23:59:59'))
//            ->orderBy('t.date', 'DESC')
//            ->getQuery()
//            ->getResult();
//    }
//
//
//    public function getSummaryForMerchant(Merchant $merchant, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
//    {
//        $result = $this->createQueryBuilder('t')
//            ->select(
//                'SUM(t.openingBalance) as opening',
//                'SUM(t.deposits) as deposits',
//                'SUM(t.sales) as sales',
//                'SUM(t.closingBalance) as closing'
//            )
//            ->andWhere('t.merchant = :merchant')
//            ->andWhere('t.transactionDate BETWEEN :startDate AND :endDate')
//            ->setParameter('merchant', $merchant)
//            ->setParameter('startDate', $startDate)
//            ->setParameter('endDate', $endDate)
//            ->getQuery()
//            ->getSingleResult();
//
//        return [
//            'opening' => $result['opening'] ?? 0,
//            'deposits' => $result['deposits'] ?? 0,
//            'sales' => $result['sales'] ?? 0,
//            'closing' => $result['closing'] ?? 0
//        ];
//    }
//}

// src/Repository/TransactionRepository.php
namespace App\Repository;

use App\Entity\Merchant;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findAllWithMerchants()
    {
        return $this->createQueryBuilder('t')
            ->addSelect('m')
            ->leftJoin('t.merchant', 'm')
            ->orderBy('m.name', 'ASC')
            ->addOrderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByMerchantAndDateRange(Merchant $merchant, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.merchant = :merchant')
            ->andWhere('t.date BETWEEN :start AND :end')
            ->setParameter('merchant', $merchant)
            ->setParameter('start', $startDate->format('Y-m-d 00:00:00'))
            ->setParameter('end', $endDate->format('Y-m-d 23:59:59'))
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getSummaryForMerchant(Merchant $merchant, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $result = $this->createQueryBuilder('t')
            ->select(
                'SUM(t.openingBalance) as opening',
                'SUM(t.deposits) as deposits',
                'SUM(t.sales) as sales',
                'SUM(t.closingBalance) as closing'
            )
            ->andWhere('t.merchant = :merchant')
            ->andWhere('t.date BETWEEN :start AND :end')
            ->setParameter('merchant', $merchant)
            ->setParameter('start', $startDate->format('Y-m-d 00:00:00'))
            ->setParameter('end', $endDate->format('Y-m-d 23:59:59'))
            ->getQuery()
            ->getSingleResult();

        return [
            'opening' => $result['opening'] ?? 0,
            'deposits' => $result['deposits'] ?? 0,
            'sales' => $result['sales'] ?? 0,
            'closing' => $result['closing'] ?? 0
        ];
    }

    public function getTotalDeposits(): float
    {
        $query = $this->createQueryBuilder('t')
            ->select('SUM(t.deposits) as total')
            ->getQuery();

        return $query->getSingleScalarResult() ?? 0;
    }

//    public function getGroupedTransactionsByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate)
//    {
//        $results = $this->createQueryBuilder('t')
//            ->select([
//                'm.id as merchantId',
//                'm.code as merchantCode',
//                'm.name as merchantName',
//                'SUM(t.openingBalance) as opening',
//                'SUM(t.deposits) as deposits',
//                'SUM(t.sales) as sales',
//                'SUM(t.closingBalance) as closing'
//            ])
//            ->join('t.merchant', 'm')
//            ->andWhere('t.date BETWEEN :start AND :end')
//            ->setParameter('start', $startDate)
//            ->setParameter('end', $endDate)
//            ->groupBy('m.id')
//            ->getQuery()
//            ->getResult();
//
//        // Format the results into the expected structure
//        $grouped = [];
//        foreach ($results as $result) {
//            $merchant = new Merchant();
//            $merchant->setId($result['merchantId']);
//            $merchant->setCode($result['merchantCode']);
//            $merchant->setName($result['merchantName']);
//            $merchant->setRegistrationDate('');
//            $merchant->setIsActive(true);
//            $merchant->setClientId('');
//            $merchant->setTransaction('');
//
//            $grouped[$result['merchantId']] = [
//                'merchant' => $merchant,
//                'totals' => [
//                    'opening' => $result['opening'],
//                    'deposits' => $result['deposits'],
//                    'sales' => $result['sales'],
//                    'closing' => $result['closing']
//                ]
//            ];
//        }
//
//        return $grouped;
//    }


// src/Repository/TransactionRepository.php
//    public function getGroupedTransactionsByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate)
//    {
//        return $this->createQueryBuilder('t')
//            ->select([
//                'm.id as merchantId',
//                'm.code as merchantCode',
//                'm.name as merchantName',
//                'SUM(t.openingBalance) as opening',
//                'SUM(t.deposits) as deposits',
//                'SUM(t.sales) as sales',
//                'SUM(t.closingBalance) as closing'
//            ])
//            ->join('t.merchant', 'm')
//            ->andWhere('t.date BETWEEN :start AND :end')
//            ->setParameter('start', $startDate)
//            ->setParameter('end', $endDate)
//            ->groupBy('m.id, m.code, m.name')
//            ->getQuery()
//            ->getResult();
//    }


}
