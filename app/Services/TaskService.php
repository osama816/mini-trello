<?php

namespace App\Services;

use App\Events\TaskAssigned;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;

class TaskService
{
    /**
     * Create a task, sync tags, and trigger assignment event if applicable.
     */
    public function createTask(array $data, ?array $tags = []): Task
    {
        $task = Task::create($data);

        if (!empty($tags)) {
            $task->tags()->sync($tags);
        }

        if ($task->assigned_to) {
            $assignedUser = User::find($task->assigned_to);
            TaskAssigned::dispatch($task, $assignedUser);
        }

        return $task;
    }

    /**
     * Update task, sync tags, and dispatch event if reassigned.
     */
    public function updateTask(Task $task, array $data, ?array $tags = null): Task
    {
        $originalAssignee = $task->assigned_to;
        $task->update($data);

        if ($tags !== null) {
            $task->tags()->sync($tags);
        }

        // Trigger assignment event if assignee changed to a valid user
        if ($task->assigned_to && $task->assigned_to !== $originalAssignee) {
            $assignedUser = User::find($task->assigned_to);
            TaskAssigned::dispatch($task, $assignedUser);
        }

        return $task;
    }

    /**
     * Delete (soft-delete) a task.
     */
    public function deleteTask(Task $task): ?bool
    {
        return $task->delete();
    }

    /**
     * Add a comment to a task.
     */
    public function addComment(Task $task, User $user, string $body): Comment
    {
        return $task->comments()->create([
            'user_id' => $user->id,
            'body' => $body,
        ]);
    }
}
