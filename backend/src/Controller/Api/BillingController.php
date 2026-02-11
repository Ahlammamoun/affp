<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BillingController extends AbstractController
{
    #[Route('/api/billing/checkout', name: 'api_billing_checkout', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function checkout(
        StripeService $stripe,
        UserRepository $users,
    ): JsonResponse {
        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();
        if (!$user) return $this->json(['message' => 'Unauthorized'], 401);

        $priceId = $_ENV['STRIPE_PRICE_ID'] ?? null;
        if (!$priceId) return $this->json(['message' => 'Missing STRIPE_PRICE_ID'], 500);

        $frontendUrl = rtrim($_ENV['FRONTEND_URL'] ?? 'http://localhost', '/');

        $client = $stripe->client();

        if (!$user->getStripeCustomerId()) {
            $customer = $client->customers->create([
                'email' => $user->getEmail(),
                'metadata' => ['user_id' => (string) $user->getId()],
            ]);
            $user->setStripeCustomerId($customer->id);
            $user->touch();
            $users->save($user);
        }

        $session = $client->checkout->sessions->create([
            'mode' => 'subscription',
            'customer' => $user->getStripeCustomerId(),
            'client_reference_id' => (string) $user->getId(),
            'line_items' => [['price' => $priceId, 'quantity' => 1]],
            'success_url' => $frontendUrl . '/abonnement/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $frontendUrl . '/abonnement/cancel',
            'metadata' => [
                'user_id' => (string) $user->getId(),
                'price_id' => (string) $priceId,
            ],
        ]);

        return $this->json(['url' => $session->url]);
    }

    /**
     * ✅ appelé depuis /abonnement/success pour activer premium immédiatement
     */
    #[Route('/api/billing/confirm', name: 'api_billing_confirm', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function confirm(
        Request $request,
        StripeService $stripe,
        UserRepository $users,
    ): JsonResponse {
        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();
        if (!$user) return $this->json(['message' => 'Unauthorized'], 401);

        $sessionId = $request->query->get('session_id');
        if (!$sessionId) return $this->json(['message' => 'Missing session_id'], 400);

        // ✅ idempotent / fast path
        $now = new \DateTimeImmutable();
        if ($user->getPremiumUntil() && $user->getPremiumUntil() > $now) {
            return $this->json([
                'ok' => true,
                'premiumUntil' => $user->getPremiumUntil()->format(DATE_ATOM),
                'already' => true,
            ]);
        }

        $client = $stripe->client();

        try {
            // ✅ 1 seul call Stripe (expand subscription)
            $session = $client->checkout->sessions->retrieve($sessionId, [
                'expand' => ['subscription'],
            ]);
        } catch (\Throwable $e) {
            // ⚠️ Ne fais pas tomber nginx : retourne pending (ou 503)
            return $this->json([
                'ok' => false,
                'status' => 'stripe_error',
                'message' => 'Stripe temporairement indisponible, réessaie.',
            ], 202);
        }

        $customerId = (string) ($session->customer ?? '');
        if (!$customerId || $user->getStripeCustomerId() !== $customerId) {
            return $this->json(['message' => 'Session mismatch'], 403);
        }

        // ✅ si pas encore finalisé
        if (($session->status ?? null) !== 'complete') {
            return $this->json(['ok' => false, 'status' => $session->status ?? null], 202);
        }

        // Optionnel : plus strict
        // if (($session->payment_status ?? null) !== 'paid') {
        //     return $this->json(['ok' => false, 'payment_status' => $session->payment_status ?? null], 202);
        // }

        $subscriptionId = (string) ($session->subscription->id ?? $session->subscription ?? '');
        $currentPeriodEnd = $session->subscription->current_period_end ?? null;

        $premiumUntil = $currentPeriodEnd
            ? (new \DateTimeImmutable())->setTimestamp((int) $currentPeriodEnd)
            : (new \DateTimeImmutable())->modify('+1 month');

        $user->setStripeSubscriptionId($subscriptionId ?: null);
        $user->setStripeSubscriptionStatus('active');
        $user->setStripePriceId($_ENV['STRIPE_PRICE_ID'] ?? null);
        $user->setPremiumUntil($premiumUntil);
        $user->touch();
        $users->save($user);

        return $this->json([
            'ok' => true,
            'premiumUntil' => $premiumUntil->format(DATE_ATOM),
        ]);
    }
}
