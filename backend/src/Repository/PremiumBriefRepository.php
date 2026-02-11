<?php

namespace App\Repository;

use App\Entity\PremiumBrief;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PremiumBriefRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PremiumBrief::class);
    }

    public function findLatestPublished(int $limit = 20): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :st')->setParameter('st', 'published')
            ->orderBy('p.publishedAt', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findOnePublishedBySlug(string $slug): ?PremiumBrief
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.slug = :slug')->setParameter('slug', $slug)
            ->andWhere('p.status = :st')->setParameter('st', 'published')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
