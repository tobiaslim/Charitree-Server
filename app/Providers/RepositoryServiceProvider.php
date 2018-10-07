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
        /*
         * Store repository service to be injected in the following format
         * Register all services in the services array into lumen container
         * 
         * "Class that requires"=>"Service that it is asking for", "Service provided"
         */
        $services = [
            "App\Http\Controllers\UserController"=>["App\Contracts\Repository\IUserRepository", "App\Services\Repository\UserRepository"]
        ];

        foreach($services as $key => $value){
            $this->app->when($key)->needs($value[0])->give($value[1]);
        }
    }
}
