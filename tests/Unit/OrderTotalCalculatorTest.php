<?php

use App\Data\OrderData;
use App\Data\OrderItemData;

uses(Tests\TestCase::class);

it('calculates total from valid items', function () {
    $items = collect([
        new OrderItemData('A', 2, 1000),
        new OrderItemData('B', 1, 500),
    ]);

    expect(OrderData::from(['items' => $items])->getTotal())->toBe(2500);
});

it('ignores invalid items and negative price', function () {
    $items = collect([
        new OrderItemData('A', 0, 1000),
        new OrderItemData('B', 2, -10),
        new OrderItemData('C', 3, 100),
    ]);

    expect(OrderData::from(['items' => $items])->getTotal())->toBe(300);
});
