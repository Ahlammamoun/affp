<?php

namespace App\Repository;

use App\Entity\Dossier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class DossierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dossier::class);
    }

    /**
     * Retour API paginé (même shape que tes articles)
     * - status: published|draft|all
     * - order: desc|asc (sur publishedAt puis id)
     * - q: recherche dans title/lead/content
     */
    public function findPaginatedForApi(
        int $page = 1,
        int $limit = 12,
        string $status = 'published',
        string $order = 'desc',
        string $q = ''
    ): array {
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';
        $q = trim($q);

        $qb = $this->createQueryBuilder('d');

        if ($status !== 'all') {
            $qb->andWhere('d.status = :status')->setParameter('status', $status);
        }

        if ($q !== '') {
            $qb->andWhere('d.title LIKE :q OR d.lead LIKE :q OR d.content LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        // tri : publishedAt puis id
        $qb->orderBy('d.publishedAt', $order)
           ->addOrderBy('d.id', $order);

        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        $paginator = new Paginator($qb->getQuery(), true);
        $total = count($paginator);
        $pages = (int) max(1, (int) ceil($total / $limit));

        $items = [];
        foreach ($paginator as $d) {
            /** @var Dossier $d */
            $items[] = [
                'id' => $d->getId(),
                'title' => $d->getTitle(),
                'slug' => $d->getSlug(),
                'lead' => $d->getLead(),
                'status' => $d->getStatus(),
                'publishedAt' => $d->getPublishedAt()?->format(DATE_ATOM),
                'createdAt' => $d->getCreatedAt()->format(DATE_ATOM),
                'updatedAt' => $d->getUpdatedAt()?->format(DATE_ATOM),
                'author' => [
                    'name' => $d->getAuthorName(),
                    'bio' => $d->getAuthorBio(),
                ],
                'thumb' => $d->getThumb(),
            ];
        }

        return [
            'items' => $items,
            'meta' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => $pages,
            ],
        ];
    }
}
