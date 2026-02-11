<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserRepository $users,
        UserPasswordHasherInterface $hasher,
    ): JsonResponse {
        $data = json_decode($request->getContent() ?: '[]', true);

        $email = isset($data['email']) ? trim((string) $data['email']) : '';
        $password = isset($data['password']) ? (string) $data['password'] : '';

        if ($email === '' || $password === '') {
            return $this->json(['message' => 'Email et mot de passe requis'], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['message' => 'Email invalide'], 400);
        }

        if (mb_strlen($password) < 6) {
            return $this->json(['message' => 'Mot de passe trop court (min 6 caractères)'], 400);
        }

        if ($users->findOneByEmail($email)) {
            return $this->json(['message' => 'Cet email existe déjà'], 409);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $user->setPremiumUntil(null);

        // si tu as ajouté les champs Stripe : laisse null au départ
        if (method_exists($user, 'setStripeCustomerId')) {
            $user->setStripeCustomerId(null);
        }
        if (method_exists($user, 'setStripeSubscriptionId')) {
            $user->setStripeSubscriptionId(null);
        }

        $hashed = $hasher->hashPassword($user, $password);
        $user->setPassword($hashed);

        $users->save($user);

        return $this->json([
            'ok' => true,
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ], 201);
    }

    #[Route('/api/refresh', name: 'api_auth_refresh', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function refresh(JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $token = $jwtManager->create($user); // ✅ prend les rôles ACTUELS (ROLE_PREMIUM si premiumUntil ok)
        return $this->json(['token' => $token]);
    }

    #[Route('/api/logout', name: 'api_auth_logout', methods: ['POST'])]
    public function __invoke(): JsonResponse
    {
        $res = new JsonResponse(['ok' => true]);

        // ⚠️ Mets LE VRAI cookie que tu utilises:
        // - si JWT cookie: "auth_token" (vu dans ton RootLayout)
        // - si session: "PHPSESSID"
        $cookieName = 'auth_token';

        $res->headers->setCookie(
            Cookie::create($cookieName)
                ->withValue('')
                ->withExpires(1)
                ->withPath('/')
                ->withHttpOnly(true)
                ->withSecure(false) // true si HTTPS
                ->withSameSite('Lax')
        );

        return $res;
    }


    #[Route('/api/password/forgot', name: 'api_password_forgot', methods: ['POST'])]
    public function forgotPassword(
        LoggerInterface $logger,
        Request $request,
        UserRepository $users,
        \Doctrine\ORM\EntityManagerInterface $em,
        MailerInterface $mailer
    ): JsonResponse {
        $logger->warning('FORGOT PASSWORD HIT');

        $data = json_decode($request->getContent() ?: '[]', true);
        $email = isset($data['email']) ? trim((string)$data['email']) : '';

        // anti-enumération: toujours OK
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $logger->warning('FORGOT PASSWORD: invalid email input');
            return $this->json(['ok' => true]);
        }

        $user = $users->findOneByEmail($email);
        if (!$user) {
            $logger->warning('FORGOT PASSWORD: user not found for ' . $email);
            return $this->json(['ok' => true]);
        }

        // token
        $rawToken = bin2hex(random_bytes(32));
        $hash = hash('sha256', $rawToken);

        $user->setResetPasswordTokenHash($hash);
        $user->setResetPasswordExpiresAt((new \DateTimeImmutable())->modify('+1 hour'));

        $em->persist($user);
        $em->flush();

        $resetLink = 'http://localhost/reset-password?token=' . $rawToken;

        $emailMessage = (new \Symfony\Component\Mime\Email())
            ->from('Wolfram et Hart <contact@wfhart.com>')
            ->replyTo('contact@wfhart.com') // optionnel mais propre
            ->to($user->getEmail())
            ->subject('Réinitialisation de mot de passe')
            ->text(
                "Bonjour,\n\n" .
                    "Clique sur ce lien pour réinitialiser ton mot de passe :\n\n" .
                    $resetLink . "\n\n" .
                    "Ce lien expire dans 1 heure."
            );

        $logger->warning('SENDING MAIL TO: ' . $user->getEmail());
        error_log('FORGOT PASSWORD HIT (error_log)');

        try {
            $mailer->send($emailMessage);
            error_log('MAILER SEND DONE');
        } catch (\Throwable $e) {
            error_log('MAIL SEND FAILED: ' . $e->getMessage() . ' (' . get_class($e) . ')');
            $logger->error('MAIL SEND FAILED', [
                'class' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            return $this->json([
                'ok' => false,
                'message' => 'MAIL_SEND_FAILED',
                'detail' => $e->getMessage(),
            ], 500);
        }


        return $this->json([
            'ok' => true,
            'sentTo' => $user->getEmail(),
        ]);
    }

    #[Route('/api/password/reset', name: 'api_password_reset', methods: ['POST'])]
    public function resetPassword(
        Request $request,
        UserRepository $users,
        UserPasswordHasherInterface $hasher,
        \Doctrine\ORM\EntityManagerInterface $em,
    ): JsonResponse {
        $data = json_decode($request->getContent() ?: '[]', true);

        $token = isset($data['token']) ? (string)$data['token'] : '';
        $newPassword = isset($data['password']) ? (string)$data['password'] : '';

        if ($token === '' || mb_strlen($newPassword) < 6) {
            return $this->json(['message' => 'Token ou mot de passe invalide'], 400);
        }

        $hash = hash('sha256', $token);

        $user = $users->findOneBy(['resetPasswordTokenHash' => $hash]);
        if (!$user) {
            return $this->json(['message' => 'Token invalide'], 400);
        }

        $expiresAt = $user->getResetPasswordExpiresAt();
        if (!$expiresAt || $expiresAt < new \DateTimeImmutable()) {
            return $this->json(['message' => 'Token expiré'], 400);
        }

        $user->setPassword($hasher->hashPassword($user, $newPassword));

        // one-time token: on efface
        $user->setResetPasswordTokenHash(null);
        $user->setResetPasswordExpiresAt(null);

        $em->persist($user);
        $em->flush();

        return $this->json(['ok' => true]);
    }
}
