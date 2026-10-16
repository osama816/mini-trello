<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogTaskAssignment
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskAssigned $event): void
    {
        $userName = $event->assignedUser ? $event->assignedUser->name : 'Unassigned';
        \Illuminate\Support\Facades\Log::info("Task [{$event->task->id}] '{$event->task->title}' was assigned to user '{$userName}'");
    }
}
