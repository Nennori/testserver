<?php

namespace App\Providers;

use App\Models\Board;
use App\Models\Task;
use App\Policies\BoardPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Board::class => BoardPolicy::class,
        Task::class => TaskPolicy::class,
    ];

    public function register()
    {
        $this->registerPolicies();
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {


    }
}
