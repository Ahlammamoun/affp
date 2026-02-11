<?php

namespace App\Controller\Api;

use App\Repository\DossierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class DossierController extends AbstractController
{
    public function __construct(
        private readonly DossierRepository $dossiers,
    ) {}

    /**
     * GET /api/dossiers
     * Query:
     *  - page=1
     *  - limit=12 (max 100)
     *  - status=published|draft|all
     *  - order=desc|asc
     *  - q=texte (optionnel)
     */
    #[Route('/dossiers', name: 'dossiers_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $page  = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, min(100, (int) $request->query->get('limit', 12)));

        $status = (string) $request->query->get('status', 'published');
        $order  = strtolower((string) $request->query->get('order', 'desc')) === 'asc' ? 'asc' : 'desc';
        $q      = trim((string) $request->query->get('q', ''));

        $data = $this->dossiers->findPaginatedForApi(
            page: $page,
            limit: $limit,
            status: $status,
            order: $order,
            q: $q
        );

        return $this->json($data);
    }

    /**
     * GET /api/dossiers/{slug}
     */
    #[Route(
        '/dossiers/{slug}',
        name: 'dossiers_show',
        methods: ['GET'],
        requirements: ['slug' => '[a-z0-9-]+']
    )]
    public function show(string $slug): JsonResponse
    {
        $d = $this->dossiers->findOneBy(['slug' => $slug]);

        if (!$d) {
            return $this->json(['error' => 'Not found'], 404);
        }

        // articles liés (id, title, slug)
        $articles = [];
        foreach ($d->getArticles() as $a) {
            $articles[] = [
                'id' => $a->getId(),
                'title' => $a->getTitle(),
                'slug' => $a->getSlug(),
            ];
        }

        return $this->json([
            'item' => [
                'id' => $d->getId(),
                'title' => $d->getTitle(),
                'slug' => $d->getSlug(),
                'lead' => $d->getLead(),
                'content' => $d->getContent(),
                'conclusion' => $d->getConclusion(),
                'author' => [
                    'name' => $d->getAuthorName(),
                    'bio' => $d->getAuthorBio(),
                ],
                'thumb' => $d->getThumb(),
                'status' => $d->getStatus(),
                'publishedAt' => $d->getPublishedAt()?->format(DATE_ATOM),
                'createdAt' => $d->getCreatedAt()->format(DATE_ATOM),
                'updatedAt' => $d->getUpdatedAt()?->format(DATE_ATOM),
                'articles' => $articles,
            ],
        ]);
    }
}
