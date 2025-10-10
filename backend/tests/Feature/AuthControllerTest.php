<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthControllerTest extends TestCase {
    use RefreshDatabase;

    #[Test]
    public function user_can_register_and_get_token() {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];
        $response = $this->postJson('/api/register', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    #[Test]
    public function user_can_login_with_correct_credentials() {
        $user = User::factory()->create(['password' => Hash::make('password123')]);
        $payload = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
            ]);
    }

    #[Test]
    public function user_cannot_login_with_invalid_credentials() {
        $user = User::factory()->create(['password' => Hash::make('password123')]);
        $payload = [
            'email' => $user->email,
            'password' => 'wrong-password',
        ];

        $response = $this->postJson('/api/login', $payload);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Invalid login',
            ]);
    }

    #[Test]
    public function authenticated_user_can_get_their_info() {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")->getJson('/api/me');
        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    #[Test]
    public function user_can_logout() {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/logout');
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully.',
            ]);
    }
}
