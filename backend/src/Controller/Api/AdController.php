<?php

namespace App\Controller\Api;

use App\Repository\AdSlideRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class AdController extends AbstractController
{
    public function __construct(private AdSlideRepository $repo) {}

    #[Route('/ads', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $slides = $this->repo->findBy(
            ['isActive' => true],
            ['position' => 'ASC']
        );

        return $this->json([
            'items' => array_map(fn($s) => [
                'badge' => $s->getBadge(),
                'title' => $s->getTitle(),
                'text' => $s->getText(),
                'href' => $s->getHref(),
            ], $slides),
        ]);
    }
}
