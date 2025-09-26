<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class OrderData extends Data
{
    public function __construct(
        #[DataCollectionOf(OrderItemData::class)]
        public DataCollection $items,
    ) {}

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->items as $item) {
            if ($item->quantity <= 0 || $item->price <= 0) {
                continue;
            }

            $total += $item->quantity * $item->price;
        }

        return $total;
    }
}
