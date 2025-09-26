<?php

namespace App\Services\Payments;

use App\Services\Payments\Gateways\CreditCardGateway;
use App\Services\Payments\Gateways\PaypalGateway;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function make(string $method): PaymentGateway
    {
        $map = config('payments.gateways');

        $method = strtolower($method);
        if (! isset($map[$method])) {
            throw new InvalidArgumentException('Unsupported payment method: '.$method);
        }

        $config = $map[$method];

        return match ($method) {
            'credit_card' => new CreditCardGateway(apiKey: $config['api_key'] ?? null),
            'paypal' => new PaypalGateway(clientId: $config['client_id'] ?? null, secret: $config['secret'] ?? null),
            default => throw new InvalidArgumentException('Unsupported payment method: '.$method),
        };
    }
}
