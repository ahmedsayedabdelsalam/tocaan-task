<?php

namespace App\Services\Payments\Gateways;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\PaymentGateway;

class PaypalGateway implements PaymentGateway
{
    public function __construct(
        private readonly ?string $clientId = null,
        private readonly ?string $secret = null,
    ) {}

    public function process(Order $order, array $payload): Payment
    {
        $successful = (bool) ($payload['simulate_success'] ?? true);

        return $order->payments()->create([
            'external_id' => $payload['external_id'] ?? null,
            'status' => $successful ? PaymentStatus::Successful : PaymentStatus::Failed,
            'method' => PaymentMethod::Paypal,
            'amount' => $order->total,
            'metadata' => [
                'payer_email' => $payload['payer_email'] ?? 'buyer@example.com',
            ],
        ]);
    }
}
