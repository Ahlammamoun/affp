<?php

namespace App\Repository;

use App\Entity\LiveUpdate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LiveUpdateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiveUpdate::class);
    }

    public function latest(int $limit = 20): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.isActive = true')
            ->orderBy('l.happenedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
