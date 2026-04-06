<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_a_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Mina Manager',
            'email' => 'manager@example.com',
            'role' => User::ROLE_MANAGER,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => 'manager@example.com',
            'role' => User::ROLE_MANAGER,
        ]);
    }

    public function test_manager_can_create_a_task_for_an_employee(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();

        $response = $this->actingAs($manager)->post(route('tasks.store'), [
            'title' => 'Prepare weekly report',
            'description' => 'Compile the current sprint progress for the team.',
            'status' => Task::STATUS_PENDING,
            'assigned_to' => $employee->id,
        ]);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'title' => 'Prepare weekly report',
            'assigned_to' => $employee->id,
            'created_by' => $manager->id,
            'status' => Task::STATUS_PENDING,
        ]);
    }

    public function test_manager_can_view_create_task_page_with_employee_select(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create([
            'name' => 'Ema Employee',
            'email' => 'ema@example.com',
        ]);

        $this->actingAs($manager)->get(route('tasks.create'))
            ->assertOk()
            ->assertSee('Create Task')
            ->assertSee('Select an employee')
            ->assertSee($employee->name)
            ->assertSee('Save Task');
    }

    public function test_employee_cannot_access_manager_only_task_pages(): void
    {
        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)->get(route('tasks.create'))->assertForbidden();
        $this->actingAs($employee)->post(route('tasks.store'), [
            'title' => 'Blocked',
            'description' => 'Blocked',
            'status' => Task::STATUS_PENDING,
            'assigned_to' => $employee->id,
        ])->assertForbidden();
    }

    public function test_employee_only_sees_their_assigned_tasks_and_can_mark_them_done(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();
        $otherEmployee = User::factory()->employee()->create();

        $assignedTask = Task::create([
            'title' => 'Finish onboarding',
            'description' => 'Complete all onboarding steps.',
            'status' => Task::STATUS_PENDING,
            'assigned_to' => $employee->id,
            'created_by' => $manager->id,
        ]);

        $otherTask = Task::create([
            'title' => 'Different employee task',
            'description' => 'This should stay hidden.',
            'status' => Task::STATUS_PENDING,
            'assigned_to' => $otherEmployee->id,
            'created_by' => $manager->id,
        ]);

        $this->actingAs($employee)->get(route('tasks.index'))
            ->assertOk()
            ->assertSee('Finish onboarding')
            ->assertDontSee('Different employee task');

        $this->actingAs($employee)->patch(route('tasks.done', $otherTask))->assertForbidden();

        $response = $this->actingAs($employee)->patch(route('tasks.done', $assignedTask));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $assignedTask->id,
            'status' => Task::STATUS_DONE,
        ]);
    }
}
