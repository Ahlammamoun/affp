<?php

namespace App\Repository;

use App\Entity\DossierReaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DossierReactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DossierReaction::class);
    }

    public function countForDossier(int $dossierId): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('SUM(r.value)')
            ->andWhere('r.dossier = :d')
            ->setParameter('d', $dossierId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function findOneByDossierAndFingerprint(int $dossierId, string $fp): ?DossierReaction
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.dossier = :d')
            ->andWhere('r.fingerprint = :f')
            ->setParameter('d', $dossierId)
            ->setParameter('f', $fp)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
