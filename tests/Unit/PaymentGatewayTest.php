<?php

use App\Enums\PaymentMethod;
use App\Services\Payments\PaymentGatewayFactory;

uses(Tests\TestCase::class);

test('payment factory return the correct payment gateway', function () {
    $paymentMethods = [
        PaymentMethod::Paypal->value => \App\Services\Payments\Gateways\PaypalGateway::class,
        PaymentMethod::CreditCard->value => \App\Services\Payments\Gateways\CreditCardGateway::class,
    ];

    foreach ($paymentMethods as $method => $expectedClass) {
        $gateway = PaymentGatewayFactory::make($method);
        expect($gateway)->toBeInstanceOf($expectedClass);
    }
});

test('payment factory throws exception for unsupported method', function () {
    PaymentGatewayFactory::make('unsupported_method');
})->throws(InvalidArgumentException::class, 'Unsupported payment method: unsupported_method');
