<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[]
     */
    public function findUpcoming(int $limit = 12): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('e')
            ->andWhere('e.status = :st')
            ->andWhere('e.eventAt >= :now')
            ->setParameter('st', Event::STATUS_PUBLISHED)
            ->setParameter('now', $now)
            ->orderBy('e.eventAt', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[]
     */
    public function findLatest(int $limit = 12): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.status = :st')
            ->setParameter('st', Event::STATUS_PUBLISHED)
            ->orderBy('e.eventAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findOnePublishedBySlug(string $slug): ?Event
{
    return $this->createQueryBuilder('e')
        ->andWhere('e.slug = :slug')
        ->setParameter('slug', $slug)
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
}

}
