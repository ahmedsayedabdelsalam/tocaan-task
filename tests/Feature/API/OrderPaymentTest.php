<?php

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('creates an order and calculates the total', function () {
    $user = User::factory()->create();
    actingAs($user, 'api');

    $items = [
        ['product' => 'Item A', 'quantity' => 2, 'price' => 1000],
        ['product' => 'Item B', 'quantity' => 1, 'price' => 500],
    ];

    $create = postJson('/api/orders', [
        'items' => $items,
    ]);

    $create->assertCreated()
        ->assertJsonPath('total', 2500)
        ->assertJsonPath('status', 'pending');
});

it('shows an order for the authenticated user', function () {
    $user = User::factory()->create();
    actingAs($user, 'api');

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'details' => [
            ['product' => 'X', 'quantity' => 1, 'price' => 700],
        ],
        'total' => 700,
        'status' => 'pending',
    ]);

    $show = getJson('/api/orders/'.$order->id);
    $show->assertOk()->assertJsonPath('total', 700);
});

it('updates an order status', function () {
    $user = User::factory()->create();
    actingAs($user, 'api');

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'details' => [
            ['product' => 'X', 'quantity' => 1, 'price' => 1000],
        ],
        'total' => 1000,
        'status' => 'pending',
    ]);

    $update = putJson('/api/orders/'.$order->id, [
        'status' => 'confirmed',
    ]);
    $update->assertOk()->assertJsonPath('status', 'confirmed');
});

it('lists orders with pagination structure', function () {
    $user = User::factory()->create();
    actingAs($user, 'api');

    Order::factory()->count(3)->create([
        'user_id' => $user->id,
        'details' => [['product' => 'X', 'quantity' => 1, 'price' => 1]],
        'total' => 1,
        'status' => 'pending',
    ]);

    $index = getJson('/api/orders');
    $index->assertOk()->assertJsonStructure(['data', 'links', 'meta']);
});

it('prevents deleting an order with existing payments', function () {
    $user = User::factory()->create();
    actingAs($user, 'api');

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'details' => [['product' => 'X', 'quantity' => 1, 'price' => 1000]],
        'total' => 1000,
        'status' => 'pending',
    ]);

    Payment::factory()->create(['order_id' => $order->id]);
    $delete = deleteJson('/api/orders/'.$order->id);
    $delete->assertUnprocessable();
});

it('rejects payment when order is not confirmed', function () {
    $user = User::factory()->create();
    actingAs($user, 'api');

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'details' => [
            ['product' => 'X', 'quantity' => 1, 'price' => 1000],
        ],
        'total' => 1000,
        'status' => 'pending',
    ]);

    $res = postJson('/api/orders/'.$order->id.'/payments/process', [
        'method' => 'credit_card',
        'payload' => ['simulate_success' => true],
    ]);
    $res->assertUnprocessable();
});

it('processes payment when order is confirmed', function () {
    $user = User::factory()->create();
    actingAs($user, 'api');

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'details' => [
            ['product' => 'X', 'quantity' => 1, 'price' => 1000],
        ],
        'total' => 1000,
        'status' => 'confirmed',
    ]);

    $ok = postJson('/api/orders/'.$order->id.'/payments/process', [
        'method' => 'credit_card',
        'payload' => ['simulate_success' => true],
    ]);
    $ok->assertCreated()->assertJsonPath('status', 'successful');
});
