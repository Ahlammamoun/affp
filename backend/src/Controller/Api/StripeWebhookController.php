<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StripeWebhookController extends AbstractController
{
    #[Route('/api/billing/webhook', name: 'api_billing_webhook', methods: ['POST'])]
    public function webhook(
        Request $request,
        StripeService $stripe,
        UserRepository $users,
    ): Response {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        $secret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? null;
        if (!$secret) {
            return new Response('Missing STRIPE_WEBHOOK_SECRET', 500);
        }

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Throwable $e) {
            return new Response('Invalid signature', 400);
        }

        // ✅ 1) checkout.session.completed -> activer premium
        if ($event->type === 'checkout.session.completed') {
            /** @var \Stripe\Checkout\Session $session */
            $session = $event->data->object;

            $customerId = $session->customer ?? null;
            $subscriptionId = $session->subscription ?? null;

            // on avait mis user_id dans metadata au checkout
            $userId = $session->metadata['user_id'] ?? null;

            $user = null;

            if ($userId) {
                $user = $users->find($userId);
            }

            if (!$user && $customerId) {
                $user = $users->findOneByStripeCustomerId((string) $customerId);
            }

            if ($user) {
                if ($customerId) $user->setStripeCustomerId((string)$customerId);
                if ($subscriptionId) $user->setStripeSubscriptionId((string)$subscriptionId);
                $user->setStripeSubscriptionStatus('active');
                $user->setStripePriceId($_ENV['STRIPE_PRICE_ID'] ?? null);

                // ✅ premium 1 mois (simple). Plus tard on prendra current_period_end via subscription.
                $user->setPremiumUntil((new \DateTimeImmutable())->modify('+1 month'));
                $user->touch();
                $users->save($user);
            }

            return new Response('ok', 200);
        }

        // ✅ 2) customer.subscription.deleted -> retirer premium
        if ($event->type === 'customer.subscription.deleted') {
            /** @var \Stripe\Subscription $sub */
            $sub = $event->data->object;

            $customerId = $sub->customer ?? null;
            $subscriptionId = $sub->id ?? null;

            $user = null;
            if ($customerId) {
                $user = $users->findOneByStripeCustomerId((string)$customerId);
            }

            if ($user) {
                if ($subscriptionId) $user->setStripeSubscriptionId((string)$subscriptionId);
                $user->setStripeSubscriptionStatus('canceled');
                $user->setPremiumUntil(null);
                $user->touch();
                $users->save($user);
            }

            return new Response('ok', 200);
        }

        // autres events ignorés
        return new Response('ignored', 200);
    }
}
