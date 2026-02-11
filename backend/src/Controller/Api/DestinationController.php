<?php

namespace App\Controller\Api;

use App\Repository\DestinationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DestinationController extends AbstractController
{
    // ✅ LISTE: GET /api/destinations?limit=12
    #[Route('/api/destinations', name: 'api_destinations_list', methods: ['GET'])]
    public function list(Request $request, DestinationRepository $repo): JsonResponse
    {
        $limit = (int) $request->query->get('limit', 12);
        if ($limit <= 0) $limit = 12;
        if ($limit > 48) $limit = 48;

        $items = array_map(static function ($d) {
            /** @var \App\Entity\Destination $d */
            return [
                'id' => $d->getId(),
                'title' => $d->getTitle(),
                'slug' => $d->getSlug(),
                'excerpt' => $d->getExcerpt(),
                'thumb' => $d->getThumb(), // /uploads/destinations/... ou URL externe
                'city' => $d->getCity(),
                'country' => $d->getCountry(),
                'publishedAt' => $d->getPublishedAt()?->format(\DateTimeInterface::ATOM),
                'isWeekly' => $d->isWeekly(),
                'link' => $d->getLink(),

            ];
        }, $repo->findPublished($limit));

        return $this->json(['items' => $items]);
    }

    // ✅ DESTINATION DE LA SEMAINE: GET /api/destinations/weekly
    // ✅ DESTINATION DE LA SEMAINE: GET /api/destinations/weekly
    #[Route('/api/destinations/weekly', name: 'api_destinations_weekly', methods: ['GET'])]
    public function weekly(DestinationRepository $repo): JsonResponse
    {
        $d = $repo->findWeekly();

        if (!$d) {
            return $this->json(['item' => null]);
        }

        // ✅ plusieurs photos: media[]
        $media = array_map(static function ($m) {
            /** @var \App\Entity\Media $m */
            return [
                'id' => $m->getId(),
                'type' => $m->getType(),
                'url' => $m->getUrl(),
                'fileName' => $m->getFileName(),
                'caption' => $m->getCaption(),
                'isMain' => $m->isMain(),
            ];
        }, $d->getMedia()->toArray());

        return $this->json([
            'item' => [
                'id' => $d->getId(),
                'title' => $d->getTitle(),
                'slug' => $d->getSlug(),
                'excerpt' => $d->getExcerpt(),
                'thumb' => $d->getThumb(),
                'city' => $d->getCity(),
                'country' => $d->getCountry(),
                'publishedAt' => $d->getPublishedAt()?->format(\DateTimeInterface::ATOM),
                'isWeekly' => $d->isWeekly(),
                'media' => $media, // ✅ IMPORTANT
                'link' => $d->getLink(),

            ],
        ]);
    }


    // ✅ DÉTAIL: GET /api/destinations/{slug}
    #[Route('/api/destinations/{slug}', name: 'api_destinations_show', methods: ['GET'])]
    public function show(string $slug, DestinationRepository $repo): JsonResponse
    {
        // ✅ uniquement publié (recommandé)
        $d = $repo->findOnePublishedBySlug($slug);

        if (!$d) {
            return $this->json(['message' => 'Destination introuvable'], 404);
        }

        // ✅ plusieurs photos: media[]
        $media = array_map(static function ($m) {
            /** @var \App\Entity\Media $m */
            return [
                'id' => $m->getId(),
                'type' => $m->getType(),         // image / video / embed
                'url' => $m->getUrl(),           // url externe si utilisée
                'fileName' => $m->getFileName(), // upload local (ex: abc.jpg)
                'caption' => $m->getCaption(),
                'isMain' => $m->isMain(),
            ];
        }, $d->getMedia()->toArray());

        return $this->json([
            'item' => [
                'id' => $d->getId(),
                'title' => $d->getTitle(),
                'slug' => $d->getSlug(),
                'excerpt' => $d->getExcerpt(),
                'content' => $d->getContent(),
                'thumb' => $d->getThumb(),
                'city' => $d->getCity(),
                'country' => $d->getCountry(),
                'publishedAt' => $d->getPublishedAt()?->format(\DateTimeInterface::ATOM),
                'isWeekly' => $d->isWeekly(),
                'media' => $media,
                'link' => $d->getLink(),

            ],
        ]);
    }
}
