<?php

namespace App\Repository;

use App\Entity\Destination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DestinationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Destination::class);
    }

    /** @return Destination[] */
    public function findPublished(int $limit = 12): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.status = :s')
            ->setParameter('s', Destination::STATUS_PUBLISHED)
            ->orderBy('d.publishedAt', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findWeekly(): ?Destination
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.status = :s')
            ->andWhere('d.isWeekly = true')
            ->setParameter('s', Destination::STATUS_PUBLISHED)
            ->orderBy('d.weeklyRank', 'ASC')
            ->addOrderBy('d.publishedAt', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOnePublishedBySlug(string $slug): ?Destination
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.slug = :slug')
            ->andWhere('d.status = :s')
            ->setParameter('slug', $slug)
            ->setParameter('s', Destination::STATUS_PUBLISHED)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
