<?php

namespace App\Repository;

use App\Entity\ApiSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ApiSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiSession::class);
    }

    // Optional: method to clean expired sessions
    public function removeExpired(): void
    {
        $qb = $this->createQueryBuilder('s')
            ->delete()
            ->where('s.expiresAt <= :now')
            ->setParameter('now', new \DateTime());
        
        $qb->getQuery()->execute();
    }
}
