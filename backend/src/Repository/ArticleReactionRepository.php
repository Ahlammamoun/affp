<?php

namespace App\Repository;

use App\Entity\ArticleReaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArticleReaction>
 */
class ArticleReactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleReaction::class);
    }

    public function countLikes(string $targetType, int $targetId): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.targetType = :t')
            ->andWhere('r.targetId = :i')
            ->andWhere('r.value = 1')
            ->setParameter('t', $targetType)
            ->setParameter('i', $targetId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findUserLike(string $fingerprint, string $targetType, int $targetId): ?ArticleReaction
    {
        return $this->findOneBy([
            'fingerprint' => $fingerprint,
            'targetType'  => $targetType,
            'targetId'    => $targetId,
            'value'       => 1,
        ]);
    }
}
