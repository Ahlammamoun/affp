<?php

namespace App\Service;

use Stripe\StripeClient;

class StripeService
{
    public function __construct(private string $stripeSecretKey)
    {
    }

    public function client(): StripeClient
    {
        return new StripeClient($this->stripeSecretKey);
    }
}
