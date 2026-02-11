<?php

namespace App\Controller\Api;

use App\Repository\SectionRepository;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


class SectionController extends AbstractController
{
    #[Route('/api/sections', name: 'api_sections', methods: ['GET'])]
    public function index(SectionRepository $sections): JsonResponse
    {
        $rows = $sections->findBy(
            ['isActive' => true],
            ['position' => 'ASC', 'name' => 'ASC']
        );

        $items = [];
        foreach ($rows as $s) {
            $slug = $s->getSlug();

            // ✅ On ignore les sections sans slug (sinon ça génère /section/undefined)
            if (!$slug || trim($slug) === '' || $slug === 'undefined') {
                continue;
            }

            $items[] = [
                'name' => $s->getName(),
                'slug' => $slug,
            ];
        }

        return $this->json([
            'items' => $items,
            'total' => count($items),
        ]);
    }


    #[Route('/api/sections/{slug}/articles', name: 'api_section_articles', methods: ['GET'])]
    public function bySection(
        string $slug,
        Request $request,
        SectionRepository $sections,
        ArticleRepository $articles
    ): JsonResponse {
        $section = $sections->findOneBy(['slug' => $slug]);
        if (!$section) {
            return $this->json(['error' => 'Section not found'], 404);
        }

        $page  = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, min(100, (int) $request->query->get('limit', 24)));

        $status = (string) $request->query->get('status', 'published');
        $order  = strtolower((string) $request->query->get('order', 'desc')) === 'asc' ? 'ASC' : 'DESC';
        $q      = trim((string) $request->query->get('q', ''));

        $data = $articles->findPaginatedForApiBySection(
            section: $section,
            page: $page,
            limit: $limit,
            status: $status,
            order: $order,
            q: $q
        );

        // Ajoute section
        $data['section'] = [
            'name' => $section->getName(),
            'slug' => $section->getSlug(),
        ];

        return $this->json($data);
    }
}
