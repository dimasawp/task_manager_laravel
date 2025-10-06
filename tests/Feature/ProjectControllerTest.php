<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProjectControllerTest extends TestCase {
    use RefreshDatabase;

    protected $user;
    protected $otherUser;

    protected function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    #[Test]
    public function user_can_see_their_own_projects() {
        $myProject = Project::factory()->create(['created_by' => $this->user->id]);
        $otherProject = Project::factory()->create(['created_by' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)->getJson('/api/projects');
        $response->assertOk()
            ->assertJsonFragment(['id' => $myProject->id])
            ->assertJsonMissing(['id' => $otherProject->id]);
    }

    // STORE
    #[Test]
    public function user_can_create_project_with_valid_data() {
        $payload = [
            'name' => 'New Project',
            'description' => 'Test project',
            'status' => 'pending',
            'deadline' => now()->addDays(5)->toDateString(),
        ];

        $response = $this->actingAs($this->user)->postJson('/api/projects', $payload);
        $response->assertCreated()->assertJsonFragment(['name' => 'New Project']);
        $this->assertDatabaseHas('projects', [
            'name' => 'New Project',
            'created_by' => $this->user->id,
        ]);
    }

    #[Test]
    public function project_creation_fails_if_name_is_empty() {
        $payload = [
            'name' => '',
            'status' => 'pending'
        ];

        $response = $this->actingAs($this->user)->postJson('/api/projects', $payload);
        $response->assertStatus(422);
    }

    #[Test]
    public function project_creation_fails_if_status_is_invalid() {
        $payload = [
            'name' => 'Invalid Project',
            'status' => 'wrong_status'
        ];

        $response = $this->actingAs($this->user)->postJson('/api/projects', $payload);
        $response->assertStatus(422);
    }

    // SHOW
    #[Test]
    public function user_can_view_their_own_project_detail() {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson("/api/projects/{$project->id}");
        $response->assertOk()->assertJsonFragment(['id' => $project->id]);
    }

    #[Test]
    public function user_cannot_view_other_users_project_detail() {
        $project = Project::factory()->create(['created_by' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)->getJson("/api/projects/{$project->id}");
        $response->assertStatus(403);
    }

    // UPDATE
    #[Test]
    public function user_can_update_their_own_project() {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $payload = ['name' => 'Updated Project'];

        $response = $this->actingAs($this->user)->putJson("/api/projects/{$project->id}", $payload);
        $response->assertOk()->assertJsonFragment(['name' => 'Updated Project']);
        $this->assertDatabaseHas('projects', ['name' => 'Updated Project']);
    }

    #[Test]
    public function user_cannot_update_others_project() {
        $project = Project::factory()->create(['created_by' => $this->otherUser->id]);
        $payload = [
            'name' => 'Hack Attempt',
            'status' => 'completed',
        ];

        $response = $this->actingAs($this->user)->putJson("/api/projects/{$project->id}", $payload);
        $response->assertStatus(403);
    }

    // DELETE
    #[Test]
    public function user_can_delete_their_own_project() {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/projects/{$project->id}");
        $response->assertNoContent();
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    #[Test]
    public function user_cannot_delete_other_users_project() {
        $project = Project::factory()->create(['created_by' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/projects/{$project->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }
}
