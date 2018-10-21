<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind('App\Services\Contracts\IUserService', 'App\Services\Concrete\UserService');
        $this->app->bind('App\Services\Contracts\ICampaignService', 'App\Services\Concrete\CampaignService');
        $this->app->bind('App\Services\Contracts\IDonationService', 'App\Services\Concrete\DonationService');

    }
}
