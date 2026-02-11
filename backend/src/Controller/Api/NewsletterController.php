<?php

namespace App\Controller\Api;

use App\Entity\NewsletterSubscriber;
use App\Repository\NewsletterSubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class NewsletterController extends AbstractController
{
    public function __construct(
        private readonly NewsletterSubscriberRepository $subscribers,
        private readonly EntityManagerInterface $em,
    ) {}

    // POST /api/newsletter/subscribe
    #[Route('/newsletter/subscribe', name: 'newsletter_subscribe', methods: ['POST'])]
    public function subscribe(Request $request): JsonResponse
    {
        // Support JSON + form-data
        $payload = $request->getContentTypeFormat() === 'json'
            ? (json_decode($request->getContent() ?: '[]', true) ?: [])
            : $request->request->all();

        $email = strtolower(trim((string)($payload['email'] ?? '')));

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Email invalide'], 400);
        }

        $subscriber = $this->subscribers->findOneBy(['email' => $email]);

        if ($subscriber) {
            // Déjà inscrit : on renvoie un statut clair
            if ($subscriber->getStatus() !== 'subscribed') {
                $subscriber->setStatus('subscribed');
                $this->em->flush();

                return $this->json([
                    'message' => 'Réinscription réussie',
                    'item' => $this->toApi($subscriber),
                ]);
            }

            return $this->json([
                'error' => 'Cet email est déjà inscrit à la newsletter.',
            ], 409);
        }

        $subscriber = new NewsletterSubscriber();
        $subscriber->setEmail($email);
        $subscriber->setStatus('subscribed');
        $subscriber->setCreatedAt(new \DateTimeImmutable());
        $subscriber->setUnsubToken(bin2hex(random_bytes(32)));

        $this->em->persist($subscriber);
        $this->em->flush();

        return $this->json([
            'message' => 'Inscription réussie',
            'item' => $this->toApi($subscriber),
        ], 201);
    }

    // POST /api/newsletter/unsubscribe
    // (pratique si tu veux désinscrire depuis une SPA)
    #[Route('/newsletter/unsubscribe', name: 'newsletter_unsubscribe', methods: ['POST'])]
    public function unsubscribe(Request $request): JsonResponse
    {
        $payload = $request->getContentTypeFormat() === 'json'
            ? (json_decode($request->getContent() ?: '[]', true) ?: [])
            : $request->request->all();

        $token = trim((string)($payload['token'] ?? ''));

        if (!$token) {
            return $this->json(['error' => 'Token manquant'], 400);
        }

        $subscriber = $this->subscribers->findOneBy(['unsubToken' => $token]);
        if (!$subscriber) {
            return $this->json(['error' => 'Token invalide'], 404);
        }

        $subscriber->setStatus('unsubscribed');
        $this->em->flush();

        return $this->json([
            'message' => 'Désinscription effectuée',
            'item' => $this->toApi($subscriber),
        ]);
    }

    // GET /api/newsletter/status?email=...
    #[Route('/newsletter/status', name: 'newsletter_status', methods: ['GET'])]
    public function status(Request $request): JsonResponse
    {
        $email = strtolower(trim((string)$request->query->get('email', '')));

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Email invalide'], 400);
        }

        $subscriber = $this->subscribers->findOneBy(['email' => $email]);

        return $this->json([
            'item' => $subscriber ? $this->toApi($subscriber) : null,
        ]);
    }

    private function toApi(NewsletterSubscriber $s): array
    {
        return [
            'id' => $s->getId(),
            'email' => $s->getEmail(),
            'status' => $s->getStatus(),
            'createdAt' => $s->getCreatedAt()?->format(DATE_ATOM),
            // ⚠️ en API publique tu peux choisir de ne PAS exposer ce token
            'unsubToken' => $s->getUnsubToken(),
        ];
    }
}
