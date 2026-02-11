<?php

namespace App\Controller\Api;

use App\Repository\DossierRepository;
use App\Repository\DossierReactionRepository;
use App\Entity\DossierReaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class DossierReactionController extends AbstractController
{
    #[Route('/api/dossiers/{slug}/reactions', methods: ['GET'])]
    public function stats(
        string $slug,
        DossierRepository $dossiers,
        DossierReactionRepository $repo,
        Request $request
    ): JsonResponse {
        $dossier = $dossiers->findOneBy(['slug' => $slug]);
        if (!$dossier) return $this->json(['likes' => 0, 'liked' => false]);

        $fp = sha1($request->getClientIp() ?? 'guest');
        $liked = (bool) $repo->findOneByDossierAndFingerprint($dossier->getId(), $fp);

        return $this->json([
            'likes' => $repo->countForDossier($dossier->getId()),
            'liked' => $liked,
        ]);
    }

    #[Route('/api/dossiers/{slug}/like', methods: ['POST'])]
    public function toggle(
        string $slug,
        DossierRepository $dossiers,
        DossierReactionRepository $repo,
        EntityManagerInterface $em,
        Request $request
    ): JsonResponse {
        $dossier = $dossiers->findOneBy(['slug' => $slug]);
        if (!$dossier) return $this->json(['likes' => 0, 'liked' => false]);

        $fp = sha1($request->getClientIp() ?? 'guest');
        $existing = $repo->findOneByDossierAndFingerprint($dossier->getId(), $fp);

        if ($existing) {
            $em->remove($existing);
            $liked = false;
        } else {
            $r = new DossierReaction();
            $r->setDossier($dossier);
            $r->setFingerprint($fp);
            $em->persist($r);
            $liked = true;
        }

        $em->flush();

        return $this->json([
            'likes' => $repo->countForDossier($dossier->getId()),
            'liked' => $liked,
        ]);
    }
}
