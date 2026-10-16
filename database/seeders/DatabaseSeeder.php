<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create 3 users (including demo account: test@example.com / password)
        $mainUser = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $otherUsers = User::factory(2)->create([
            'password' => bcrypt('password'),
        ]);

        $allUsers = collect([$mainUser])->concat($otherUsers);

        // 2. Create 8 tags
        $tagData = [
            ['name' => 'Bug', 'color' => '#ef4444'],
            ['name' => 'Feature', 'color' => '#3b82f6'],
            ['name' => 'Urgent', 'color' => '#dc2626'],
            ['name' => 'Frontend', 'color' => '#10b981'],
            ['name' => 'Backend', 'color' => '#8b5cf6'],
            ['name' => 'Design', 'color' => '#ec4899'],
            ['name' => 'DevOps', 'color' => '#f59e0b'],
            ['name' => 'Review', 'color' => '#06b6d4'],
        ];

        $tags = collect($tagData)->map(fn ($tag) => \App\Models\Tag::create($tag));

        // 3. Create 5 projects (3 for main user, 2 for other users)
        $projects = collect();
        $projects->push(\App\Models\Project::factory()->create([
            'user_id' => $mainUser->id,
            'name' => 'Mini Trello App',
            'description' => 'A modern task and project management Kanban system.',
        ]));
        $projects->push(\App\Models\Project::factory()->create([
            'user_id' => $mainUser->id,
            'name' => 'E-Commerce Platform',
            'description' => 'Online shopping cart and payment gateway integration.',
        ]));
        $projects->push(\App\Models\Project::factory()->create([
            'user_id' => $mainUser->id,
            'name' => 'Mobile App API',
            'description' => 'RESTful endpoints for iOS and Android applications.',
        ]));

        foreach ($otherUsers as $user) {
            $projects->push(\App\Models\Project::factory()->create([
                'user_id' => $user->id,
            ]));
        }

        // 4. Create 30 tasks distributed among projects
        $statuses = ['todo', 'doing', 'done'];
        for ($i = 1; $i <= 30; $i++) {
            $project = $projects->random();
            $task = \App\Models\Task::factory()->create([
                'project_id' => $project->id,
                'assigned_to' => $allUsers->random()->id,
                'status' => $statuses[array_rand($statuses)],
            ]);

            // Attach 1 to 3 random tags
            $task->tags()->attach($tags->random(rand(1, 3))->pluck('id'));

            // Optionally add 1-2 comments
            if (rand(0, 1)) {
                \App\Models\Comment::create([
                    'task_id' => $task->id,
                    'user_id' => $allUsers->random()->id,
                    'body' => fake()->sentence(8),
                ]);
            }
        }
    }
}
