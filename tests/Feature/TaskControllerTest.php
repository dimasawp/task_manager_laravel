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
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tasks', $payload);
        $response->assertCreated()->assertJsonFragment(['title' => 'My New Task']);

        $this->assertDatabaseHas('tasks', [
            'title' => 'My New Task',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function creating_task_requires_title() {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tasks', []);
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

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated title',
        ]);
        $response->assertOk()->assertJsonFragment(['title' => 'Updated title']);
    }

    #[Test]
    public function user_cannot_update_other_users_task() {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/tasks/{$task->id}", [
            'title' => 'Hack Update',
        ]);
        $response->assertForbidden();
    }

    #[Test]
    public function updating_task_requires_valid_data() {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/tasks/{$task->id}", [
            'title' => '',
        ]);
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
