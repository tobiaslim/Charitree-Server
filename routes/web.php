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

//route group user. 
$router->group(['prefix' => 'user'], function () use ($router) {
    $router->post('create', 'UserController@register');
    $router->post('authenticate', "UserController@authenticate");
    $router->post('test',['middleware'=>Auth::class, "uses"=>"UserController@testauthorization"]);
});
