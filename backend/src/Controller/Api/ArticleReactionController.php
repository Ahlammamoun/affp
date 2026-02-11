<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\ArticleReaction;
use App\Entity\Dossier;
use App\Repository\ArticleReactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ArticleReactionController extends AbstractController
{
    private function fingerprint(Request $request): string
    {
        // Si tu veux un fingerprint front (localStorage) tu peux l’envoyer via X-Fingerprint
        $fp = $request->headers->get('X-Fingerprint');

        // fallback simple
        if (!$fp) {
            $ua = (string) $request->headers->get('User-Agent', '');
            $ip = (string) $request->getClientIp();
            $fp = substr(hash('sha256', $ip . '|' . $ua), 0, 64);
        }

        return $fp;
    }

    // -------------------------
    // ARTICLE
    // -------------------------

    #[Route('/articles/{slug}/reactions', name: 'api_article_reactions', methods: ['GET'])]
    public function articleReactions(
        string $slug,
        Request $request,
        EntityManagerInterface $em,
        ArticleReactionRepository $repo
    ): JsonResponse {
        $article = $em->getRepository(Article::class)->findOneBy(['slug' => $slug]);
        if (!$article) {
            return $this->json(['error' => 'Article not found'], 404);
        }

        $fp = $this->fingerprint($request);
        $targetType = ArticleReaction::TYPE_ARTICLE;
        $targetId = $article->getId();

        $likes = $repo->countLikes($targetType, $targetId);
        $liked = $repo->findUserLike($fp, $targetType, $targetId) !== null;

        return $this->json([
            'likes' => $likes,
            'liked' => $liked,
        ]);
    }

    #[Route('/articles/{slug}/like', name: 'api_article_like', methods: ['POST'])]
    public function articleLike(
        string $slug,
        Request $request,
        EntityManagerInterface $em,
        ArticleReactionRepository $repo
    ): JsonResponse {
        $article = $em->getRepository(Article::class)->findOneBy(['slug' => $slug]);
        if (!$article) {
            return $this->json(['error' => 'Article not found'], 404);
        }

        $fp = $this->fingerprint($request);
        $targetType = ArticleReaction::TYPE_ARTICLE;
        $targetId = $article->getId();

        $existing = $repo->findUserLike($fp, $targetType, $targetId);

        if ($existing) {
            $em->remove($existing);
            $liked = false;
        } else {
            $r = new ArticleReaction();
            $r->setFingerprint($fp);
            $r->setValue(1);
            $r->setCreatedAt(new \DateTimeImmutable());
            $r->setTargetType($targetType);
            $r->setTargetId($targetId);
            $em->persist($r);
            $liked = true;
        }

        $em->flush();

        return $this->json([
            'likes' => $repo->countLikes($targetType, $targetId),
            'liked' => $liked,
        ]);
    }

    // -------------------------
    // DOSSIER
    // -------------------------

    #[Route('/dossiers/{slug}/reactions', name: 'api_dossier_reactions', methods: ['GET'])]
    public function dossierReactions(
        string $slug,
        Request $request,
        EntityManagerInterface $em,
        ArticleReactionRepository $repo
    ): JsonResponse {
        $dossier = $em->getRepository(Dossier::class)->findOneBy(['slug' => $slug]);
        if (!$dossier) {
            return $this->json(['error' => 'Dossier not found'], 404);
        }

        $fp = $this->fingerprint($request);
        $targetType = ArticleReaction::TYPE_DOSSIER;
        $targetId = $dossier->getId();

        $likes = $repo->countLikes($targetType, $targetId);
        $liked = $repo->findUserLike($fp, $targetType, $targetId) !== null;

        return $this->json([
            'likes' => $likes,
            'liked' => $liked,
        ]);
    }

    #[Route('/dossiers/{slug}/like', name: 'api_dossier_like', methods: ['POST'])]
    public function dossierLike(
        string $slug,
        Request $request,
        EntityManagerInterface $em,
        ArticleReactionRepository $repo
    ): JsonResponse {
        $dossier = $em->getRepository(Dossier::class)->findOneBy(['slug' => $slug]);
        if (!$dossier) {
            return $this->json(['error' => 'Dossier not found'], 404);
        }

        $fp = $this->fingerprint($request);
        $targetType = ArticleReaction::TYPE_DOSSIER;
        $targetId = $dossier->getId();

        $existing = $repo->findUserLike($fp, $targetType, $targetId);

        if ($existing) {
            $em->remove($existing);
            $liked = false;
        } else {
            $r = new ArticleReaction();
            $r->setFingerprint($fp);
            $r->setValue(1);
            $r->setCreatedAt(new \DateTimeImmutable());
            $r->setTargetType($targetType);
            $r->setTargetId($targetId);
            $em->persist($r);
            $liked = true;
        }

        $em->flush();

        return $this->json([
            'likes' => $repo->countLikes($targetType, $targetId),
            'liked' => $liked,
        ]);
    }
}
