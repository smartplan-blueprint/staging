<?php

namespace App\Repository\Merchant;

use App\Entity\Merchant\PortalUserDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PortalUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PortalUserDetails::class);
    }

    public function findForMerchant(int $merchantId): array
    {
        return $this->createQueryBuilder('pu')
            ->andWhere('pu.merchant = :merchantId')
            ->setParameter('merchantId', $merchantId)
            ->orderBy('pu.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find portal user with merchant details
     */
    public function findOneWithMerchant(int $id): ?PortalUserDetails
    {
        return $this->createQueryBuilder('pu')
            ->leftJoin('pu.merchant', 'm')
            ->addSelect('m') // Important for performance
            ->where('pu.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByMerchant(int $merchantId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.merchant = :merchantId')
            ->setParameter('merchantId', $merchantId)
            ->getQuery()
            ->getResult();
    }
    /**
     * Find all portal users for a merchant with merchant details
     */
    public function findByMerchantWithDetails(int $merchantId): array
    {
        return $this->createQueryBuilder('pu')
            ->leftJoin('pu.merchant', 'm')
            ->addSelect('m')
            ->where('m.id = :merchantId')
            ->setParameter('merchantId', $merchantId)
            ->getQuery()
            ->getResult();
    }

    public function getWeeklyActivity(): array
    {
        $startDate = new \DateTime('-7 days');

        $query = $this->createQueryBuilder('u')
            ->select('u.dateCreated')
            ->where('u.dateCreated >= :startDate')
            ->setParameter('startDate', $startDate)
            ->getQuery();

        $results = $query->getResult();

        // Process data in PHP
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $activityData = array_fill_keys($daysOfWeek, 0);

        foreach ($results as $result) {
            if ($result['dateCreated'] instanceof \DateTime) {
                $dayName = $result['dateCreated']->format('l');
                $activityData[$dayName]++;
            }
        }

        return $activityData;
    }
}
