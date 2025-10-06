<?php

use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskControllerTest extends TestCase {
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;

    public function setUp(): void {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    #[Test]
    public function user_can_see_their_own_tasks_with_relations() {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'project_id' => $project->id,
            'title' => 'My Task',
        ]);

        Task::factory()->create(['user_id' => $this->otherUser->id, 'title' => 'Other Task']);

        $response = $this->actingAs($this->user)->getJson('/api/tasks');
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'My Task'])
            ->assertJsonMissing(['title' => 'Other Task'])
            ->assertJsonStructure(['data' => [['category', 'project', 'subtasks']]]);
    }

    // STORE
    #[Test]
    public function user_can_create_a_simple_task() {
        $payload = [
            'title' => 'My New Task',
            'status' => 'pending',
            'priority' => 'medium',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/tasks', $payload);
        $response->assertCreated()->assertJsonFragment(['title' => 'My New Task']);
        $this->assertDatabaseHas('tasks', [
            'title' => 'My New Task',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function user_can_create_task_with_project() {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $payload = [
            'title' => 'Task With Project',
            'project_id' => $project->id,
            'status' => 'in_progress',
            'priority' => 'high',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/tasks', $payload);
        $response->assertCreated();
        $this->assertDatabaseHas('tasks', [
            'title' => 'Task With Project',
            'project_id' => $project->id,
        ]);
    }

    #[Test]
    public function user_can_create_subtask_one_level_only() {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $payload = [
            'title' => 'Subtask Level 1',
            'parent_id' => $task->id,
            'status' => 'pending',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/tasks', $payload);
        $response->assertCreated();
        $this->assertDatabaseHas('tasks', ['parent_id' => $task->id]);
    }

    #[Test]
    public function cannot_create_subtask_of_a_subtask() {
        $parent = Task::factory()->create(['user_id' => $this->user->id]);
        $subtask = Task::factory()->create(['user_id' => $this->user->id, 'parent_id' => $parent->id]);

        $payload = [
            'title' => 'Invalid Subtask',
            'parent_id' => $subtask->id,
            'status' => 'pending',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/tasks', $payload);
        $response->assertStatus(422);
    }

    #[Test]
    public function validation_fails_if_title_is_empty() {
        $payload = [
            'title' => '',
            'status' => 'pending',
            'priority' => 'medium',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/tasks', $payload);
        $response->assertStatus(422);
    }

    #[Test]
    public function validation_fails_if_status_or_priority_invalid() {
        $payload = [
            'title' => 'Invalid Data',
            'status' => 'wrong_status',
            'priority' => 'invalid_priority',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/tasks', $payload);
        $response->assertStatus(422);
    }

    // SHOW
    #[Test]
    public function user_can_view_their_own_task_detail_with_relations() {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $subtask = Task::factory()->create(['user_id' => $this->user->id, 'parent_id' => $task->id]);

        $response = $this->actingAs($this->user)->getJson("/api/tasks/{$task->id}");
        $response->assertOk()
            ->assertJsonFragment(['id' => $task->id])
            ->assertJsonStructure(['data' => ['subtasks', 'project', 'category', 'parent']]);
    }

    #[Test]
    public function user_cannot_view_other_users_task_detail() {
        $task = Task::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)->getJson("/api/tasks/{$task->id}");
        $response->assertStatus(403);
    }

    // UPDATE
    #[Test]
    public function user_can_update_thier_own_task() {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $payload = [
            'title' => 'Updated Task',
            'status' => 'completed'
        ];

        $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", $payload);
        $response->assertOk()->assertJsonFragment(['title' => 'Updated Task']);
        $this->assertDatabaseHas('tasks', ['title' => 'Updated Task']);
    }

    #[Test]
    public function cannot_update_subtask_with_invalid_parent() {
        $parent = Task::factory()->create(['user_id' => $this->user->id]);
        $subtask = Task::factory()->create(['user_id' => $this->user->id, 'parent_id' => $parent->id]);
        $payload = ['parent_id' => $subtask->id]; // invalid nesting

        $response = $this->actingAs($this->user)->putJson("/api/tasks/{$parent->id}", $payload);
        $response->assertStatus(422);
    }

    #[Test]
    public function updating_task_requires_with_empty_name() {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $payload = [
            'title' => '',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/tasks/{$task->id}", $payload);
        $response->assertStatus(422);
    }

    #[Test]
    public function user_cannot_update_other_users_task() {
        $task = Task::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", ['title' => 'Hack']);
        $response->assertStatus(403);
    }

    // DESTROY
    #[Test]
    public function user_can_delete_their_own_task() {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/tasks/{$task->id}");
        $response->assertNoContent();
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function user_cannot_delete_other_uses_task() {
        $task = Task::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/tasks/{$task->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
}
