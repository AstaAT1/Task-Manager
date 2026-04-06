<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $tasks = Task::query()
            ->with(['assignedTo', 'creator'])
            ->when(
                ! $request->user()->can('manage-tasks'),
                fn ($query) => $query->where('assigned_to', $request->user()->id)
            )
            ->latest()
            ->paginate(10);

        return view('tasks.index', [
            'tasks' => $tasks,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-tasks');

        return view('tasks.create', [
            'employees' => $this->employees(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-tasks');

        Task::create([
            ...$this->validatedTaskData($request),
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('status', 'Task created successfully.');
    }

    public function markDone(Task $task): RedirectResponse
    {
        Gate::authorize('mark-task-done', $task);

        $task->update([
            'status' => Task::STATUS_DONE,
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('status', 'Task marked as done.');
    }

    /**
     * @return Collection<int, User>
     */
    private function employees(): Collection
    {
        return User::query()
            ->where('role', User::ROLE_EMPLOYEE)
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedTaskData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', Rule::in([Task::STATUS_PENDING, Task::STATUS_DONE])],
            'assigned_to' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(
                    fn ($query) => $query->where('role', User::ROLE_EMPLOYEE)
                ),
            ],
        ]);
    }
}
