<?php

// src/Repository/MerchantRepository.php
namespace App\Repository;

use App\Entity\Merchant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MerchantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Merchant::class);
    }

    /**
     * Find merchant with details by ID
     */
    public function findWithDetails(int $id): ?Merchant
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.details', 'd')
            ->addSelect('d') // Important for performance
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByIdWithCreditLimits(int $id): ?Merchant
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.creditLimits', 'cl')
            ->addSelect('cl')
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    /**
     * Find all merchants with their details
     */
    public function findAllWithDetails(): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.details', 'd')
            ->addSelect('d')
            ->getQuery()
            ->getResult();
    }

    // Add custom repository methods here
    public function findByCode(string $code): ?Merchant
    {
        return $this->findOneBy(['code' => $code]);
    }

    // In MerchantRepository
    public function findActiveMerchantsWithDetails(): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.details', 'd')
            ->addSelect('d')
            ->where('d.isActive = true')
            ->getQuery()
            ->getResult();
    }

    public function searchMerchantsWithDetails(string $searchTerm): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.details', 'd')
            ->addSelect('d')
            ->where('m.name LIKE :term OR d.contactName LIKE :term')
            ->setParameter('term', '%'.$searchTerm.'%')
            ->getQuery()
            ->getResult();
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function save(Merchant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Merchant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllActive()
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBySearchTerm(string $searchTerm)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.name LIKE :searchTerm OR m.code LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->orderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
