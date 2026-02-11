<?php

namespace App\Controller\Api;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/events')]
class EventController extends AbstractController
{
    #[Route('/upcoming', methods: ['GET'])]
    public function upcoming(EventRepository $repo, Request $request): JsonResponse
    {
        $limit = (int) ($request->query->get('limit', 12));
        $limit = max(1, min(50, $limit));

        $items = $repo->findUpcoming($limit);

        return $this->json([
            'items' => array_map(static function ($e) {
                return [
                    'id' => $e->getId(),
                    'title' => $e->getTitle(),
                    'slug' => $e->getSlug(),
                    'category' => $e->getCategory(),
                    'city' => $e->getCity(),
                    'country' => $e->getCountry(),
                    'eventAt' => $e->getEventAt()?->format(DATE_ATOM),
                    'thumb' => $e->getThumb(), // ✅ upload => /uploads/events/xxx
                    'link' => $e->getLink(),
                    'description' => $e->getDescription(),
                ];
            }, $items),
        ]);
    }

    #[Route('', methods: ['GET'])]
    public function list(EventRepository $repo, Request $request): JsonResponse
    {
        $limit = (int) ($request->query->get('limit', 12));
        $limit = max(1, min(50, $limit));

        $items = $repo->findLatest($limit);

        return $this->json([
            'items' => array_map(static function ($e) {
                return [
                    'id' => $e->getId(),
                    'title' => $e->getTitle(),
                    'slug' => $e->getSlug(),
                    'category' => $e->getCategory(),
                    'city' => $e->getCity(),
                    'country' => $e->getCountry(),
                    'eventAt' => $e->getEventAt()?->format(DATE_ATOM),
                    'thumb' => $e->getThumb(),
                    'link' => $e->getLink(),
                    'description' => $e->getDescription(),
                ];
            }, $items),
        ]);
    }

    // ✅ DÉTAIL: GET /api/events/{slug}
    #[Route('/{slug}', name: 'api_events_show', methods: ['GET'])]
    public function show(string $slug, EventRepository $repo): JsonResponse
    {
        $ev = $repo->findOnePublishedBySlug($slug); // ou findOneBy(['slug'=>$slug]) selon ton projet

        if (!$ev) {
            return $this->json(['message' => 'Événement introuvable'], 404);
        }

        return $this->json([
            'item' => [
                'id' => $ev->getId(),
                'title' => $ev->getTitle(),
                'slug' => $ev->getSlug(),
                'category' => $ev->getCategory(),
                'city' => $ev->getCity(),
                'country' => $ev->getCountry(),
                'eventAt' => $ev->getEventAt()?->format(\DateTimeInterface::ATOM),
                'thumb' => $ev->getThumb(),
                'description' => $ev->getDescription(),
                'link' => $ev->getLink(),
            ],
        ]);
    }
}
