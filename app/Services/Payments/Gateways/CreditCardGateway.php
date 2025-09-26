<?php

namespace App\Services\Payments\Gateways;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\PaymentGateway;

class CreditCardGateway implements PaymentGateway
{
    public function __construct(
        private readonly ?string $apiKey = null,
    ) {}

    public function process(Order $order, array $payload): Payment
    {
        $successful = (bool) ($payload['simulate_success'] ?? true);

        return $order->payments()->create([
            'external_id' => $payload['external_id'] ?? null,
            'status' => $successful ? PaymentStatus::Successful : PaymentStatus::Failed,
            'method' => PaymentMethod::CreditCard,
            'amount' => $order->total,
            'metadata' => [
                'card_last4' => $payload['card_last4'] ?? '4242',
            ],
        ]);
    }
}
