<?php

namespace App\Controller\Api;

use App\Repository\LiveUpdateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class LiveController extends AbstractController
{
    public function __construct(private readonly LiveUpdateRepository $repo) {}

    #[Route('/live', name: 'live_latest', methods: ['GET'])]
    public function latest(Request $req): JsonResponse
    {
        $limit = max(1, min(50, (int) $req->query->get('limit', 10)));
        $items = $this->repo->latest($limit);

        return $this->json([
            'items' => array_map(fn($l) => [
                'id' => $l->getId(),
                'time' => $l->getHappenedAt()->format('H:i'),
                'tag' => $l->getTag() ?? '',
                'title' => $l->getTitle(),
                'href' => $l->getArticle()
                    ? '/article/' . $l->getArticle()->getSlug()
                    : ($l->getUrl() ?? null),
            ], $items),
        ]);
    }
}
