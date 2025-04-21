<?php

namespace Modules\TodoList\App\Observers;

use Illuminate\Support\Facades\Log;
use Modules\TodoList\App\Models\Todo;

class TodoObserver
{
    /**
     * Handle the Todo "created" event.
     */
    public function created(Todo $todo): void
    {
        Log::info("Todo created: ID {$todo->id}, Title: {$todo->title}");
        activity()
           ->performedOn($todo)
           ->causedBy(auth()->user())
           ->withProperties(['attributes' => $todo->getAttributes()])
           ->log('Created todo item');
    }

    /**
     * Handle the Todo "updated" event.
     */
    public function updated(Todo $todo): void
    {
        Log::info("Todo updated: ID {$todo->id}, Title: {$todo->title}");
        activity()
           ->performedOn($todo)
           ->causedBy(auth()->user())
           ->withProperties([
                'old' => $todo->getOriginal(),
                'new' => $todo->getAttributes()
            ])
           ->log('Updated todo item');
    }

    /**
     * Handle the Todo "deleted" event.
     */
    public function deleted(Todo $todo): void
    {
        Log::info("Todo deleted: ID {$todo->id}, Title: {$todo->title}");
        activity()
           ->performedOn($todo)
           ->causedBy(auth()->user())
           ->withProperties(['attributes' => $todo->getOriginal()])
           ->log('Deleted todo item');
    }

    /**
     * Handle the Todo "restored" event.
     */
    public function restored(Todo $todo): void
    {
        // Optional: Log restoration if needed
        // activity()
        //    ->performedOn($todo)
        //    ->causedBy(auth()->user())
        //    ->log('Restored todo item');
    }

    /**
     * Handle the Todo "force deleted" event.
     */
    public function forceDeleted(Todo $todo): void
    {
        // Optional: Log force deletion if needed
        // activity()
        //    ->performedOn($todo) // Or log something generic as the model might be gone
        //    ->causedBy(auth()->user())
        //    ->withProperties(['attributes' => $todo->getOriginal()])
        //    ->log('Force deleted todo item');
    }
} 