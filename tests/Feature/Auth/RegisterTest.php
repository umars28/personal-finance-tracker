<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully()
    {
        $payload = [
            'name' => 'Umar Sabirin',
            'email' => 'umar@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                         'token',
                     ],
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'umar@example.com',
        ]);
    }

    public function test_register_fails_with_invalid_data()
    {
        $payload = [
            'name' => '',
            'email' => 'not-an-email',
            'password' => '123',
            'password_confirmation' => '321',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
