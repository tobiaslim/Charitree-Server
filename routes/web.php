<?php
use App\Http\Middleware\Authenticate as Auth;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {  
    return $router->app->version();
});

/**
 * Routes:
 * POST /users                   Creating a new user
 */
$router->group(['prefix' => 'users'], function () use ($router) {
    $router->post('', 'UserController@register');                   
});

/**
 * Routes:
 * POST     /sessions               Create a new session
 * GET      /sessions               Check if a session is valid using auth 
 */
$router->group(['prefix' => 'sessions'], function () use ($router) {
    $router->post('', 'SessionController@createSession');
    $router->get('',['middleware'=>Auth::class, "uses"=>"SessionController@testauthorization"]);
});