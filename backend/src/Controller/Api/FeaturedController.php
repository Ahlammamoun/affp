<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\Media;
use App\Repository\FeaturedSlotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class FeaturedController extends AbstractController
{
    public function __construct(
        private readonly FeaturedSlotRepository $featuredSlotRepository,
    ) {}

    #[Route('/featured/{key}', name: 'featured_by_key', methods: ['GET'])]
    public function byKey(string $key): JsonResponse
    {
        $slot = $this->featuredSlotRepository->findActiveByKey($key);

        if (!$slot || !$slot->getArticle()) {
            return $this->json(['item' => null], 200);
        }

        $a = $slot->getArticle();
        $thumb = $this->resolveThumbFromArticle($a);
        $dateLabel = $a->getPublishedAt() ?? $a->getCreatedAt();

        return $this->json([
            'item' => [
                'key' => $slot->getSlotKey(),
                'article' => [
                    'id' => $a->getId(),
                    'title' => $a->getTitle(),
                    'slug' => $a->getSlug(),
                    'excerpt' => $a->getExcerpt(),
                    'thumb' => $thumb,
                    'publishedAt' => $a->getPublishedAt()?->format(DATE_ATOM),
                    'createdAt' => $a->getCreatedAt()->format(DATE_ATOM),
                    'updatedAt' => $a->getUpdatedAt()?->format(DATE_ATOM),
                    'dateLabel' => $dateLabel?->format(DATE_ATOM),
                ],
            ],
        ]);
    }

    private function resolveThumbFromArticle(Article $a): ?string
    {
        $media = $a->getMedia();

        if ($media->isEmpty()) return null;

        /** @var Media|null $main */
        $main = null;
        foreach ($media as $m) {
            if ($m->isMain()) { $main = $m; break; }
        }

        /** @var Media|null $pick */
        $pick = $main ?? $media->first();

        if (!$pick instanceof Media) return null;

        if ($pick->getFileName()) return '/uploads/media/' . $pick->getFileName();

        return $pick->getUrl();
    }
}
