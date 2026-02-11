<?php

namespace App\Controller\Api;

use App\Repository\PremiumBriefRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/premium-briefs')]
class PremiumBriefController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    
    public function list(Request $request, PremiumBriefRepository $repo): JsonResponse
    {
        $limit = (int) $request->query->get('limit', 20);
        $limit = max(1, min(50, $limit));

        // filtres simples (optionnels)
        $scope = $request->query->get('scope'); // "afrique" | "region" | "pays" | "theme"
        $tag   = $request->query->get('tag');   // ex: "sahel"

        $qb = $repo->createQueryBuilder('p')
            ->andWhere('p.status = :st')->setParameter('st', 'published')
            ->orderBy('p.publishedAt', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults($limit);

        if (is_string($scope) && $scope !== '') {
            $qb->andWhere('p.scope = :scope')->setParameter('scope', $scope);
        }

        // tags = JSON, on filtre côté PHP (simple & safe)
        $items = $qb->getQuery()->getResult();

        if (is_string($tag) && $tag !== '') {
            $items = array_values(array_filter($items, static function ($p) use ($tag) {
                $tags = $p->getTags() ?? [];
                return in_array($tag, $tags, true);
            }));
        }

        return $this->json([
            'items' => array_map(static function ($p) {
                return [
                    'id' => $p->getId(),
                    'title' => $p->getTitle(),
                    'slug' => $p->getSlug(),
                    'scope' => $p->getScope(),
                    'scopeLabel' => $p->getScopeLabel(),
                    'tags' => $p->getTags(),
                    'bullets' => $p->getBullets(),
                    // on peut envoyer un "teaser" si tu veux (ex: 1ère bullet)
                    'publishedAt' => $p->getPublishedAt()?->format(\DateTimeInterface::ATOM),
                ];
            }, $items),
        ]);
    }

    // ✅ DÉTAIL: GET /api/premium-briefs/{slug}
    #[Route('/{slug}', methods: ['GET'])]
        #[IsGranted('ROLE_PREMIUM')] // 🔒 ici
    public function show(string $slug, PremiumBriefRepository $repo): JsonResponse
    {
        $p = $repo->findOnePublishedBySlug($slug);

        if (!$p) {
            return $this->json(['message' => 'Résumé premium introuvable'], 404);
        }

        return $this->json([
            'item' => [
                'id' => $p->getId(),
                'title' => $p->getTitle(),
                'slug' => $p->getSlug(),
                'scope' => $p->getScope(),
                'scopeLabel' => $p->getScopeLabel(),
                'tags' => $p->getTags(),
                'bullets' => $p->getBullets(),
                'summaryHtml' => $p->getSummaryHtml(), // contenu premium
                'publishedAt' => $p->getPublishedAt()?->format(\DateTimeInterface::ATOM),
                'createdAt' => $p->getCreatedAt()->format(\DateTimeInterface::ATOM),
                'updatedAt' => $p->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
            ],
        ]);
    }
}
