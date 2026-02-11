<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

class JwtCookieSuccessListener
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $response = $event->getResponse();

        $token = $data['token'] ?? null;
        if (!$token || !$response) {
            return;
        }

        $cookie = Cookie::create('auth_token')
            ->withValue($token)
            ->withPath('/')
            ->withHttpOnly(true)
            ->withSecure(false) // ✅ mets true en HTTPS prod
            ->withSameSite('Lax');

        $response->headers->setCookie($cookie);

        // Optionnel : tu peux aussi éviter de renvoyer le token en JSON
        // $event->setData(['ok' => true]);
    }
}
