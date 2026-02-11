<?php

namespace App\Controller\Api;

use App\Repository\ArticleRepository;
use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $articles,
        private readonly SectionRepository $sections,
    ) {}

    // GET /api/articles
    #[Route('/articles', name: 'articles_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $page  = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, min(100, (int) $request->query->get('limit', 24)));

        $status = (string) $request->query->get('status', 'published');
        $order  = strtolower((string) $request->query->get('order', 'desc')) === 'asc' ? 'asc' : 'desc';
        $q      = trim((string) $request->query->get('q', ''));

        $data = $this->articles->findPaginatedForApi(
            page: $page,
            limit: $limit,
            status: $status,
            order: $order,
            q: $q
        );

        return $this->json($data);
    }

    // GET /api/articles/{slug}
    #[Route(
        '/articles/{slug}',
        name: 'articles_show',
        methods: ['GET'],
        requirements: ['slug' => '(?!must-read$)[a-z0-9-]+']
    )]    public function show(string $slug): JsonResponse
    {
        $a = $this->articles->findOneBy(['slug' => $slug]);
        if (!$a) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $media = [];
        foreach ($a->getMedia() as $m) {
            $media[] = [
                'type' => $m->getType(),
                'url' => $m->getUrl(),
                'fileName' => $m->getFileName(),
                'caption' => $m->getCaption(),
                'isMain' => $m->isMain(),
            ];
        }

        return $this->json([
            'item' => [
                'id' => $a->getId(),
                'title' => $a->getTitle(),
                'slug' => $a->getSlug(),
                'excerpt' => $a->getExcerpt(),
                'content' => $a->getContent(),
                'status' => $a->getStatus(),
                'publishedAt' => $a->getPublishedAt()?->format(DATE_ATOM),
                'createdAt' => $a->getCreatedAt()?->format(DATE_ATOM),
                'updatedAt' => $a->getUpdatedAt()?->format(DATE_ATOM),
                'section' => $a->getSection() ? [
                    'name' => $a->getSection()->getName(),
                    'slug' => $a->getSection()->getSlug(),
                ] : null,
                'media' => $media,
            ],
        ]);
    }

    // GET /api/articles/must-read
    #[Route('/articles/must-read', name: 'articles_must_read', methods: ['GET'])]
    public function mustRead(): JsonResponse
    {
        $a = $this->articles->findOneMustRead();

        if (!$a) {
            return $this->json(['item' => null]);
        }

        $medias = $a->getMedia()->toArray();
        $secondary = null;
        $main = null;

        foreach ($medias as $m) {
            if ($m->isMain()) $main = $m;
            else $secondary = $m;
        }

        $pick = $secondary ?: $main;
        $thumb = null;

        if ($pick) {
            if ($pick->getFileName()) {
                $thumb = '/uploads/media/' . ltrim($pick->getFileName(), '/');
            } elseif ($pick->getUrl()) {
                $thumb = $pick->getUrl();
            }
        }

        return $this->json([
            'item' => [
                'id' => $a->getId(),
                'title' => $a->getTitle(),
                'slug' => $a->getSlug(),
                'excerpt' => $a->getExcerpt(),
                'publishedAt' => $a->getPublishedAt()?->format(DATE_ATOM),
                'thumb' => $thumb,
            ],
        ]);
    }
}
