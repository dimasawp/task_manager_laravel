<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskControllerTest extends TestCase {
    use RefreshDatabase;

    #[Test]
    public function user_can_view_their_own_tasks() {
        $user = User::factory()->create();
        $tasks = Task::factory()
            ->count(2)
            ->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks');
        $response->assertOk()->assertJsonCount(2);
    }

    #[Test]
    public function user_cannot_see_tasks_of_other_users_in_index() {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Task::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks');
        $response->assertOk()->assertJsonCount(0);
    }

    // STORE
    #[Test]
    public function user_can_create_task_with_valid_data() {
        $user = User::factory()->create();
        $payload = [
            'title' => 'My New Task',
            'description' => 'New Task',
            // Tambahan setelah update add priority feature: nullable bisa insert/bisa insert tanpa priority
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tasks', $payload);
        $response->assertCreated()->assertJsonFragment(['title' => 'My New Task']);

        $this->assertDatabaseHas('tasks', [
            'title' => 'My New Task',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function user_can_set_task_priority_when_creating_task() {
        $user = User::factory()->create();
        $payload = [
            'title' => 'Priority Task',
            'description' => 'Priority Task',
            'priority' => 'high',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tasks', $payload);
        $response->assertCreated()->assertJsonFragment(['priority' => 'high']);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Priority Task',
            'user_id' => $user->id,
            'priority' => 'high',
        ]);
    }

    #[Test]
    public function invalid_priority_fails_validation_on_store() {
        $user = User::factory()->create();
        $payload = [
            'title' => 'Priority Task',
            'priority' => 'extreme',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tasks', $payload);
        $response->assertStatus(422);
    }

    #[Test]
    public function creating_task_requires_title() {
        $user = User::factory()->create();
        $payload = [];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tasks', $payload);
        $response->assertStatus(422);
    }

    // SHOW
    #[Test]
    public function user_can_view_their_own_task_detail() {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/tasks/{$task->id}");
        $response->assertOk()->assertJsonFragment(['id' => $task->id]);
    }

    #[Test]
    public function user_cannot_view_other_users_task_detial() {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/tasks/{$task->id}");
        $response->assertForbidden();
    }

    // UPDATE
    #[Test]
    public function user_can_update_thier_task() {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $payload = [
            'title' => 'Updated title',
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/tasks/{$task->id}", $payload);
        $response->assertOk()->assertJsonFragment(['title' => 'Updated title']);
    }

    #[Test]
    public function user_cannot_update_other_users_task() {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $other->id]);
        $payload = [
            'title' => 'Hack Update',
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/tasks/{$task->id}", $payload);
        $response->assertForbidden();
    }

    #[Test]
    public function updating_task_requires_valid_data() {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $payload = [
            'title' => '',
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/tasks/{$task->id}", $payload);
        $response->assertStatus(422);
    }

    #[Test]
    public function user_can_update_task_priority() {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'priority' => null]);
        $payload = [
            'title' => 'Test',
            'priority' => 'medium',
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/tasks/{$task->id}", $payload);
        $response->assertOk()->assertJsonFragment(['id' => $task->id, 'priority' => 'medium']);
    }

    #[Test]
    public function updating_task_with_invalid_priority_fails() {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'priority' => null]);
        $payload = [
            'priority' => 'extreme',
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/tasks/{$task->id}", $payload);
        $response->assertStatus(422);
    }

    // DESTROY
    #[Test]
    public function user_can_delete_their_task() {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/tasks/{$task->id}");
        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function user_cannot_delete_other_uses_task() {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/tasks/{$task->id}");
        $response->assertForbidden();
    }
}
