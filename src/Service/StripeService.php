<?php

namespace App\Service;

use Stripe\StripeClient;

class StripeService
{
    private $stripe;
    private string $secretPublicKey;

    public function __construct(string $stripeSecretKey, string $stripePublicKey)
    {
        $this->stripe = new StripeClient($stripeSecretKey);
        $this->stripePublicKey = $stripePublicKey;
    }

    public function createCheckoutSession(float $amount, string $currency, string $successUrl, string $cancelUrl)
    {
        return $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => 'Paiement Task'],
                    'unit_amount' => $amount * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }

    public function getPublicKey(): string
    {
        return $this->stripePublicKey;
    }
}
