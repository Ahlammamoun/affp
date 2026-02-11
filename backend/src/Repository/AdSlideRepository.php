<?php

namespace App\Repository;

use App\Entity\AdSlide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdSlide>
 */
class AdSlideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdSlide::class);
    }

    /**
     * @return AdSlide[]
     * Retourne les slides actifs, triés par position
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isActive = true')
            ->orderBy('a.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return AdSlide[]
     * Retourne tous les slides (même inactifs) triés
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
