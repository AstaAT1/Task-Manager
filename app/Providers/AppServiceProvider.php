<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-tasks', fn (User $user): bool => $user->isManager());

        Gate::define('mark-task-done', function (User $user, Task $task): bool {
            return $user->isEmployee() && $task->assigned_to === $user->id;
        });

        Blade::if('manager', fn (): bool => auth()->check() && auth()->user()->isManager());
        Blade::if('employee', fn (): bool => auth()->check() && auth()->user()->isEmployee());
    }
}
