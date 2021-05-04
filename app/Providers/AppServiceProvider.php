<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Encore\Admin\AdminServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
//        $this->app->register(App\Providers\ResponseMacroServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
