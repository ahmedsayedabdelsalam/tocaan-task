<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ProcessPaymentData extends Data
{
    public function __construct(
        public string $method,
        public array $payload = [],
    ) {}
}
