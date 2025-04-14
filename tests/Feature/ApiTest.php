<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function testLogin()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Login successful!',
                 ]);
    }

    public function testAddCashier()
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager, 'sanctum');

        $response = $this->postJson('/api/user/addCachier', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secretpassword',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Cashier added successfully!',
                 ]);
    }

    public function testSearch()
    {
        $cashier = User::factory()->cashier()->create();

        $response = $this->postJson('/api/search', [
            'query' => $cashier->name,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Search successful!',
                 ]);
    }

    public function testGetCashiers()
    {
        $manager = User::factory()->manager()->create();
        User::factory()->cashier()->count(2)->create();

        $this->actingAs($manager, 'sanctum');

        $response = $this->getJson('/api/user/cashiers');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
    }

    public function testDeleteCashier()
    {
        $manager = User::factory()->manager()->create();
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($manager, 'sanctum');

        $response = $this->deleteJson("/api/user/cashiers/{$cashier->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Cashier deleted successfully!',
                 ]);
    }
}
