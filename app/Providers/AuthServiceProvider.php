<?php

namespace App\Providers;
use App\Policies\BoardPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Board::class => BoardPolicy::class,
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
