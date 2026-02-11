<?php

namespace App\Repository;

use App\Entity\ArticleCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleCard::class);
    }

    /**
     * Cards actives (optionnel: limité)
     */
    public function findActive(int $limit = 12): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.isActive = :on')
            ->setParameter('on', true)
            ->orderBy('c.publishedAt', 'DESC')
            ->addOrderBy('c.id', 'DESC');

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}
