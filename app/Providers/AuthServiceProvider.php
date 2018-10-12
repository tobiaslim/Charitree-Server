<?php

namespace App\Providers;

use App\Models\User as User;
use App\Models\Session as Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        //key[0] is username, key[1] is user token
        //must be of the same instance to be valid
        // 1. Find session $token
        // 2. Check if user tied to session has the same email as $key[0]
        // 3. store result in $user variable. Store (user object) and return to auth service if true, NULL if false
        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                //extracting user email and session token
                $payload = explode(' ', $request->header('Authorization'));
                $key = explode(':', base64_decode($payload[1]));
                $user = null;
                /*
                Session verification begins here
                */
                //1.
                $session = Session::where('session_token', $key[1])->where('session_expire', '>', new \DateTime())->first();
                if (!empty($session)) {
                    $user = ($session->user->email == $key[0]) ? $session->user : null;
                    if (!empty($user)) {
                        $request->request->add(['user' => $user]);
                    }
                }
                return $user;
            }
        });
    }
}
