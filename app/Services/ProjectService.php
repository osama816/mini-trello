<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectService
{
    /**
     * Get paginated projects for a given user with tasks count breakdown.
     */
    public function getPaginatedProjectsForUser(User $user, int $perPage = 9): LengthAwarePaginator
    {
        return $user->projects()
            ->withCount([
                'tasks',
                'tasks as todo_tasks_count' => fn ($q) => $q->where('status', 'todo'),
                'tasks as doing_tasks_count' => fn ($q) => $q->where('status', 'doing'),
                'tasks as done_tasks_count' => fn ($q) => $q->where('status', 'done'),
            ])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create a new project for a user.
     */
    public function createProject(User $user, array $data): Project
    {
        return $user->projects()->create($data);
    }

    /**
     * Get project details with filtered tasks (avoiding N+1).
     */
    public function getProjectBoardData(Project $project, array $filters = []): array
    {
        $query = $project->tasks()
            ->with(['assignedUser', 'tags', 'comments.user'])
            ->latest();

        // Search filter
        if (!empty($filters['q'])) {
            $query->search($filters['q']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->status($filters['status']);
        }

        // Tag filter
        if (!empty($filters['tag'])) {
            $query->whereHas('tags', fn ($q) => $q->where('tags.id', $filters['tag']));
        }

        $tasks = $query->get();
        $users = User::select('id', 'name', 'email')->get();
        $tags = Tag::all();

        return [
            'project' => $project,
            'tasks' => $tasks,
            'users' => $users,
            'tags' => $tags,
            'filters' => [
                'q' => $filters['q'] ?? null,
                'status' => $filters['status'] ?? null,
                'tag' => $filters['tag'] ?? null,
            ],
        ];
    }

    /**
     * Update an existing project.
     */
    public function updateProject(Project $project, array $data): bool
    {
        return $project->update($data);
    }

    /**
     * Delete (soft-delete) a project.
     */
    public function deleteProject(Project $project): ?bool
    {
        return $project->delete();
    }
}
