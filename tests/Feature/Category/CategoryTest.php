<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        return ['Authorization' => "Bearer $token"];
    }

    public function test_can_list_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories', $this->authenticate());

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'type', 'created_at', 'updated_at']
                     ]
                 ]);
    }

    public function test_can_create_category()
    {
        $payload = [
            'name' => 'Makan',
            'type' => 'expense'
        ];

        $response = $this->postJson('/api/categories', $payload, $this->authenticate());

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Makan']);
    }

    public function test_can_show_category()
    {
        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}", $this->authenticate());

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $category->id]);
    }

    public function test_can_update_category()
    {
        $category = Category::factory()->create();

        $payload = [
            'name' => 'Transportasi',
            'type' => 'expense'
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $payload, $this->authenticate());

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Transportasi']);
    }

    public function test_can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}", [], $this->authenticate());

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Category deleted successfully']);
    }
}
