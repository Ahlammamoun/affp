<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Section;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Pagination + filtres pour l'API
     */
    public function findPaginatedForApi(
        int $page,
        int $limit,
        string $status = 'published',
        string $order = 'desc',
        string $q = ''
    ): array {
        $qb = $this->createQueryBuilder('a');

        // Filtre status
        if ($status !== 'all' && $status !== '') {
            $qb->andWhere('a.status = :status')
                ->setParameter('status', $status);
        }

        // Recherche simple
        if ($q !== '') {
            $qb->andWhere('a.title LIKE :q OR a.excerpt LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        // Tri
        $dir = strtolower($order) === 'asc' ? 'ASC' : 'DESC';
        $qb->addOrderBy('a.publishedAt', $dir)
            ->addOrderBy('a.createdAt', $dir);

        // Total
        $countQb = clone $qb;
        $total = (int) $countQb->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $offset = ($page - 1) * $limit;

        /** @var Article[] $items */
        $items = $qb->select('a')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        // Sérialisation light
        $serialized = array_map(static function (Article $a): array {
            return [
                'id'          => $a->getId(),
                'title'       => $a->getTitle(),
                'slug'        => $a->getSlug(),
                'excerpt'     => $a->getExcerpt(),
                'status'      => $a->getStatus(),
                'publishedAt' => $a->getPublishedAt()?->format(DATE_ATOM),
                'createdAt'   => $a->getCreatedAt()?->format(DATE_ATOM),
                'updatedAt'   => $a->getUpdatedAt()?->format(DATE_ATOM),
            ];
        }, $items);

        return [
            'items' => $serialized,
            'meta' => [
                'page'  => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => (int) ceil(max(1, $total) / $limit),
            ],
        ];
    }

    public function findPaginatedForApiBySection(
        \App\Entity\Section $section,
        int $page = 1,
        int $limit = 24,
        string $status = 'published',
        string $order = 'DESC',
        string $q = ''
    ): array {
        $qb = $this->createQueryBuilder('a')
            // ✅ charge les medias (évite lazy + N+1)
            ->leftJoin('a.media', 'm')
            ->addSelect('m')
            ->andWhere('a.section = :section')
            ->setParameter('section', $section)
            ->andWhere('a.status = :status')
            ->setParameter('status', $status);

        if ($q !== '') {
            $qb->andWhere('a.title LIKE :q OR a.excerpt LIKE :q OR a.content LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        $qb->orderBy('a.publishedAt', $order)
            ->addOrderBy('a.createdAt', $order);

        $countQb = clone $qb;
        $total = (int) $countQb->select('COUNT(DISTINCT a.id)')->getQuery()->getSingleScalarResult();

        $articles = $qb->select('a')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'items' => array_map(function (\App\Entity\Article $a) {

                $medias = $a->getMedia()->toArray();

                // ✅ priorité au media "secondaire" (isMain = false)
                $secondary = null;
                $main = null;

                foreach ($medias as $m) {
                    if ($m->isMain()) {
                        $main = $m;
                    } else {
                        $secondary = $m; // si plusieurs secondaires, prend le dernier
                    }
                }

                $pick = $secondary ?: $main;

                // ✅ URL publique
                $thumb = null;
                if ($pick) {
                    if ($pick->getFileName()) {
                        $thumb = '/uploads/media/' . ltrim($pick->getFileName(), '/');
                    } elseif ($pick->getUrl()) {
                        $thumb = $pick->getUrl();
                    }
                }

                return [
                    'id' => $a->getId(),
                    'title' => $a->getTitle(),
                    'slug' => $a->getSlug(),
                    'excerpt' => $a->getExcerpt(),
                    'publishedAt' => $a->getPublishedAt()?->format(DATE_ATOM),
                    'createdAt' => $a->getCreatedAt()?->format(DATE_ATOM),

                    // ✅ ce champ sera utilisé par Next pour afficher l'image à droite
                    'thumb' => $thumb,
                ];
            }, $articles),
        ];
    }

    public function findOneMustRead(): ?\App\Entity\Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isMustRead = :yes')
            ->setParameter('yes', true)
            ->andWhere('a.status = :status')
            ->setParameter('status', 'published')
            ->orderBy('a.mustReadRank', 'ASC')
            ->addOrderBy('a.publishedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
