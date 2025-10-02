<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryControllerTest extends TestCase {
    use RefreshDatabase;

    #[Test]
    public function user_can_view_thier_categories() {
        $user = User::factory()->create();
        $categories = Category::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/categories');
        $response->assertOk()->assertJsonCount(3, 'data');
    }

    #[Test]
    public function user_cannot_view_other_user_categories() {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $categories = Category::factory()->count(3)->create([
            'user_id' => $other->id
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/categories');
        $response->assertOk()->assertJsonCount(0, 'data');
    }

    // STORE
    #[Test]
    public function user_can_create_new_category() {
        $user = User::factory()->create();
        $payload = [
            'name' => "Urgent",
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/categories', $payload);
        $response->assertCreated()->assertJsonFragment(['name' => 'Urgent']);

        $this->assertDatabaseHas('categories', [
            'name' => 'Urgent',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function user_cannot_create_new_category_without_name() {
        $user = User::factory()->create();
        $payload = [
            'name' => '',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/categories', $payload);
        $response->assertStatus(422);
    }

    #[Test]
    public function user_cannot_create_new_category_for_another_user() {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $payload = [
            'name' => "Urgent",
            'user_id' => $other->id,
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/categories', $payload);
        $response->assertCreated();
        $this->assertDatabaseHas('categories', [
            'name' => 'Urgent',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseMissing('categories', [
            'name' => 'Urgent',
            'user_id' => $other->id,
        ]);
    }

    // UPDATE
    #[Test]
    public function user_can_update_their_category() {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $payload = [
            'name' => "Low",
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/categories/{$category->id}", $payload);
        $response->assertOk()->assertJsonFragment(['name' => 'Low']);

        $this->assertDatabaseHas('categories', [
            'name' => 'Low',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function user_cannot_update_their_category_without_name() {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $payload = [
            'name' => "",
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/categories/{$category->id}", $payload);
        $response->assertStatus(422);
    }

    #[Test]
    public function user_cannot_update_other_user_category() {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $payload = [
            'name' => "Low",
            'user_id' => $other->id,
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/categories/{$category->id}", $payload);
        $response->assertOk();

        $this->assertDatabaseHas('categories', [
            'name' => 'Low',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseMissing('categories', [
            'name' => 'Low',
            'user_id' => $other->id,
        ]);
    }

    // DELETE
    #[Test]
    public function user_can_delete_their_category() {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/categories/{$category->id}");
        $response->assertNoContent();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    #[Test]
    public function user_cannot_delete_other_user_category() {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/categories/{$category->id}");
        $response->assertForbidden();
    }
}
