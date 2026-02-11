<?php

namespace App\Repository;

use App\Entity\FeaturedSlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FeaturedSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeaturedSlot::class);
    }

    /**
     * Récupère un slot actif par sa clé
     * ex: portrait_du_jour
     */
    public function findActiveByKey(string $key): ?FeaturedSlot
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.slotKey = :key')
            ->setParameter('key', $key)
            ->orderBy('f.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
