<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * Bind UserRepository implementation to IUserRepository for all classess
         */
        $this->app->bind('App\Contracts\Repository\IUserRepository', 'App\Services\Repository\UserRepository');
    }
}