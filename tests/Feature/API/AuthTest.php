<?php

use Illuminate\Support\Facades\Auth;

test('registers a new user', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Zero@404',
        'password_confirmation' => 'Zero@404',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

test('does not expose user info on duplicate registration', function () {
    $userEmail = 'duplicate@example.com';
    \App\Models\User::factory()->create([
        'email' => $userEmail,
    ]);

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Duplicate User',
        'email' => $userEmail,
        'password' => 'Zero@404',
        'password_confirmation' => 'Zero@404',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Registration request received. If the account can be created, you will receive an email.',
    ]);
});

test('logs in a user', function () {
    $user = \App\Models\User::factory()->create([
        'password' => bcrypt('Zero@404'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'Zero@404',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
});

test('fetches authenticated user details', function () {
    $user = \App\Models\User::factory()->create();
    $token = Auth::guard('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/auth/me');

    $response->assertStatus(200);
    $response->assertJson(['email' => $user->email]);
});

test('logs out a user', function () {
    $user = \App\Models\User::factory()->create();
    $token = Auth::guard('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->deleteJson('/api/auth/logout');

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Successfully logged out']);
});

test('refreshes a token', function () {
    $user = \App\Models\User::factory()->create();
    $token = Auth::guard('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/auth/refresh');

    $response->assertStatus(200);
    $response->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
});

test('does not register a user with a weak password', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Weak Password User',
        'email' => 'weakpassword@example.com',
        'password' => '123456',
        'password_confirmation' => '123456',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);
});
