<?php

namespace Tests\Feature\Transaction;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        return ['Authorization' => 'Bearer ' . $user->createToken('test')->plainTextToken];
    }

    public function test_can_list_transactions()
    {
        $headers = $this->authenticate();
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Transaction::factory()->count(3)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/transactions', $headers);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }

    public function test_can_create_transaction()
    {
        $user = User::factory()->create();
        $this->actingAs($user); // autentikasi user
        $headers = ['Accept' => 'application/json'];

        $category = Category::factory()->create();

        $payload = [
            'amount' => 150000,
            'type' => 'income',
            'category_id' => $category->id,
            'description' => 'Test income',
            'date' => now()->toDateString(),
        ];

        $response = $this->postJson('/api/transactions', $payload, $headers);

        $response->assertStatus(201)
                ->assertJsonFragment(['message' => 'Transaction created successfully']);
    }

    public function test_can_show_transaction()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $headers = ['Accept' => 'application/json'];

        $category = Category::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson("/api/transactions/{$transaction->id}", $headers);

        $response->assertStatus(200)
                ->assertJsonFragment(['message' => 'Transaction retrieved successfully']);
    }

    public function test_can_update_transaction()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $headers = ['Accept' => 'application/json'];

        $category = Category::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $payload = [
            'amount' => 500000,
            'type' => 'expense',
            'category_id' => $category->id,
            'description' => 'Updated transaction',
            'date' => now()->toDateString(),
        ];

        $response = $this->putJson("/api/transactions/{$transaction->id}", $payload, $headers);

        $response->assertStatus(200)
                ->assertJsonFragment(['message' => 'Transaction updated successfully']);
    }

    public function test_can_delete_transaction()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $headers = ['Accept' => 'application/json'];

        $category = Category::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->deleteJson("/api/transactions/{$transaction->id}", [], $headers);

        $response->assertStatus(200)
                ->assertJsonFragment(['message' => 'Transaction deleted successfully']);
    }

    public function test_filter_transactions_by_category_and_type()
    {
        $user = User::factory()->create();

        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category1->id,
            'type' => 'expense',
        ]);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category2->id,
            'type' => 'income',
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/transactions/filter?category_id='.$category1->id.'&type=expense');

        $response->assertStatus(200)
                 ->assertJson([
                    'success' => true,
                 ])
                 ->assertJsonCount(1, 'data');
    }

    public function test_monthly_summary_returns_expected_structure()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 100000,
            'date' => now()->startOfMonth(),
        ]);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 50000,
            'date' => now()->startOfMonth(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/summary/monthly');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_income',
                    'total_expense',
                    'ending_balance',
                    'transactions_per_category' => [
                        ['category_id', 'category_name', 'transaction_count']
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_income' => 100000,
                    'total_expense' => 50000,
                    'ending_balance' => 50000
                ]
            ]);
    }

}