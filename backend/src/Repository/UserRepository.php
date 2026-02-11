<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Utilisé par le login (email)
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('LOWER(u.email) = :email')
            ->setParameter('email', mb_strtolower($email))
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findOneByStripeCustomerId(string $customerId): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.stripeCustomerId = :cid')
            ->setParameter('cid', $customerId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Liste des users premium actifs
     */
    public function findPremiumActive(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.premiumUntil IS NOT NULL')
            ->andWhere('u.premiumUntil > :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('u.premiumUntil', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Liste des admins
     * (roles stockés en JSON)
     */
    public function findAdmins(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('JSON_CONTAINS(u.roles, :role) = 1')
            ->setParameter('role', json_encode('ROLE_ADMIN'))
            ->getQuery()
            ->getResult();
    }

    /**
     * Compter les utilisateurs
     */
    public function countUsers(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Sauvegarde rapide
     */
    public function save(User $user, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->persist($user);

        if ($flush) {
            $em->flush();
        }
    }

    public function remove(User $user, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($user);

        if ($flush) {
            $em->flush();
        }
    }
}
