<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class OrderItemData extends Data
{
    public function __construct(
        public string $product,
        public int $quantity,
        public int $price,
    ) {}
}
