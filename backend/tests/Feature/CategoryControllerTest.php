<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryControllerTest extends TestCase {
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;

    public function setUp(): void {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    #[Test]
    public function user_can_view_thier_own_categories() {
        $categories = Category::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/categories');
        $response->assertOk()->assertJsonCount(3, 'data');
    }

    #[Test]
    public function user_cannot_view_other_user_categories() {
        $categories = Category::factory()->count(3)->create([
            'user_id' => $this->otherUser->id
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/categories');
        $response->assertOk()->assertJsonCount(0, 'data');
    }

    // STORE
    #[Test]
    public function user_can_create_new_category() {
        $payload = [
            'name' => "Urgent",
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/categories', $payload);
        $response->assertCreated()->assertJsonFragment(['name' => 'Urgent']);
        $this->assertDatabaseHas('categories', [
            'name' => 'Urgent',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function user_cannot_create_new_category_without_name() {
        $payload = [
            'name' => '',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/categories', $payload);
        $response->assertStatus(422);
    }

    #[Test]
    public function user_cannot_create_new_category_for_another_user() {
        $payload = [
            'name' => "Urgent",
            'user_id' => $this->otherUser->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/categories', $payload);
        $response->assertCreated();
        $this->assertDatabaseHas('categories', [
            'name' => 'Urgent',
            'user_id' => $this->user->id,
        ]);
        $this->assertDatabaseMissing('categories', [
            'name' => 'Urgent',
            'user_id' => $this->otherUser->id,
        ]);
    }

    // SHOW
    #[Test]
    public function user_can_view_their_own_category_detail() {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson("/api/categories/{$category->id}");
        $response->assertOk()->assertJsonFragment(['id' => $category->id]);
    }

    #[Test]
    public function user_cannot_view_other_users_category_detail() {
        $category = Category::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)->getJson("/api/categories/{$category->id}");
        $response->assertStatus(403);
    }

    // UPDATE
    #[Test]
    public function user_can_update_their_own_category() {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $payload = [
            'name' => "Low",
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/categories/{$category->id}", $payload);
        $response->assertOk()->assertJsonFragment(['name' => 'Low']);
        $this->assertDatabaseHas('categories', [
            'name' => 'Low',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function user_cannot_update_their_own_category_without_name() {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $payload = [
            'name' => "",
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/categories/{$category->id}", $payload);
        $response->assertStatus(422);
    }

    #[Test]
    public function user_cannot_update_other_user_category() {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $payload = [
            'name' => "Low",
            'user_id' => $this->otherUser->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/categories/{$category->id}", $payload);
        $response->assertOk();
        $this->assertDatabaseHas('categories', [
            'name' => 'Low',
            'user_id' => $this->user->id,
        ]);
        $this->assertDatabaseMissing('categories', [
            'name' => 'Low',
            'user_id' => $this->otherUser->id,
        ]);
    }

    // DELETE
    #[Test]
    public function user_can_delete_their_own_category() {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/categories/{$category->id}");
        $response->assertNoContent();
        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    #[Test]
    public function user_cannot_delete_other_user_category() {
        $category = Category::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/categories/{$category->id}");
        $response->assertForbidden();
    }
}
