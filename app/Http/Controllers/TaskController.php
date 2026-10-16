<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $project = Project::findOrFail($request->project_id);
        Gate::authorize('view', $project);

        $this->taskService->createTask(
            $request->safe()->except(['tags']),
            $request->tags ?? []
        );

        return back()->with('success', 'Task created successfully!');
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        Gate::authorize('view', $task->project);

        $this->taskService->updateTask(
            $task,
            $request->safe()->except(['tags']),
            $request->has('tags') ? $request->tags : null
        );

        return back()->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified task (Soft delete).
     */
    public function destroy(Task $task): RedirectResponse
    {
        Gate::authorize('view', $task->project);

        $this->taskService->deleteTask($task);

        return back()->with('success', 'Task deleted successfully!');
    }

    /**
     * Store a comment on the specified task.
     */
    public function storeComment(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('view', $task->project);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $this->taskService->addComment($task, $request->user(), $validated['body']);

        return back()->with('success', 'Comment added successfully!');
    }
}
