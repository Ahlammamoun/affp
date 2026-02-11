<?php

namespace App\Controller\Api;

use App\Repository\ArticleCardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ArticleCardController extends AbstractController
{
    #[Route('/api/article-cards', name: 'api_article_cards', methods: ['GET'])]
    public function list(Request $request, ArticleCardRepository $repo): JsonResponse
    {
        $limit = (int) $request->query->get('limit', 12);
        if ($limit <= 0) $limit = 12;
        if ($limit > 48) $limit = 48;

        $cards = $repo->findActive($limit);

        $items = array_map(static function ($c) {
            /** @var \App\Entity\ArticleCard $c */
            return [
                'id' => $c->getId(),
                'title' => $c->getTitle(),
                'slug' => $c->getSlug(),
                'excerpt' => $c->getExcerpt(),
                'thumb' => $c->getThumb(),
                'author' => $c->getAuthor(),
                'publishedAt' => $c->getPublishedAt()?->format(\DateTimeInterface::ATOM),
                'link' => $c->getLink(),
            ];
        }, $cards);

        return $this->json(['items' => $items]);
    }

    #[Route('/api/article-cards/{slug}', name: 'api_article_card_show', methods: ['GET'])]
    public function show(string $slug, ArticleCardRepository $repo): JsonResponse
    {
        $card = $repo->findOneBy(['slug' => $slug]);

        if (!$card) {
            return $this->json(['message' => 'Article card introuvable'], 404);
        }

        return $this->json([
            'item' => [
                'id' => $card->getId(),
                'title' => $card->getTitle(),
                'slug' => $card->getSlug(),
                'excerpt' => $card->getExcerpt(),
                'thumb' => $card->getThumb(),
                'author' => $card->getAuthor(),
                'publishedAt' => $card->getPublishedAt()?->format(\DateTimeInterface::ATOM),
                'link' => $card->getLink(),
            ]
        ]);
    }
}
