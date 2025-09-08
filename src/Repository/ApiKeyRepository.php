<?php

namespace App\Repository;

use App\Entity\ApiKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiKey>
 *
 * @method ApiKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiKey[]    findAll()
 * @method ApiKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiKey::class);
    }

    // Example custom method: find active key by secret
    public function findActiveBySecret(string $secretKey): ?ApiKey
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.secretKey = :secret')
            ->andWhere('a.isActive = :active')
            ->setParameter('secret', $secretKey)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

