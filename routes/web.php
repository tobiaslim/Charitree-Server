<?php
use App\Http\Middleware\Authenticate as Auth;
use App\Http\Middleware\CampaignManagerMiddleware as CM;

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
 * HTTP VERBS: GET PUT POST DELETE PATCH
 * 
 * GET - Retrieving / Reading
 * POST - Creating new / Creating new 
 * PUT - Create / edit
 * DELETE - Delete
 * PATCH - EDIT PARTS
 */

/**
 * Routes:
 * POST     /users                                  Creating a new user
 * POST     /users/campaignmanager                  Create campaign manager from a user
 * GET      /users/campaignmanager                  Get current session campaign manager details 
 * PUT      /users                                  Edit user
 */
$router->group(['prefix' => 'users'], function () use ($router) {
    $router->post('', 'UserController@register');
    $router->post('/campaignmanager',['middleware'=>[Auth::class], "uses"=>"UserController@registerAsCampaignManager"]);
    $router->get('/campaignmanager',['middleware'=>[Auth::class, CM::class], "uses"=>"UserController@getCurrentCampaignManagerDetails"]); 
    $router->put('', ['middleware'=>[Auth::class], "uses"=>"UserController@editUser"]);
});

/**
 * Routes:
 * POST     /addresses                              Create address for a user 
 * GET      /addresses                              Get all addresses for a user
 */
$router->group(['prefix' => 'addresses'], function () use ($router) {
    $router->post('',['middleware'=>[Auth::class],"uses"=>"AddressController@addAddresses"]);                 
    $router->get('',['middleware'=>[Auth::class],"uses"=>"AddressController@getAddressByUser"]);
});

/**
 * Routes:
 * POST     /sessions               Create a new session
 * GET      /sessions               Check if a session is valid using auth 
 */
$router->group(['prefix' => 'sessions'], function () use ($router) {
    $router->post('', 'SessionController@createSession');
    $router->get('',['middleware'=>[Auth::class], "uses"=>"SessionController@testauthorization"]);
});

/**
 * Routes:
 * GET      /items                                  Get list of items
 */
$router->get('/items', "ItemController@getItems");


/**
 * Routes:
 * GET      /campaigns                              Get all campaigns
 * POST     /campaigns                              Create campaign
 * 
 */
$router->group(['prefix' => 'campaigns'], function () use ($router) {
    $router->get('/', "CampaignController@getCampaigns");
    $router->post('', ['middleware'=>[Auth::class, CM::class], "uses"=>"CampaignController@createCampaign"]);
});

/**
 * Routes:
 * GET      /donations                              Get all donations of the user based on the user's session
 * POST     /donations/campaigns/{campaignID}       Create donation for a campaign ID.
 * GET      /donations/campaigns/{campaignID}       Get all campaigns
 * PATCH    /donations/{donationID}                 Cancel Donation
 * 
 */
$router->group(['prefix' => 'donations'], function () use ($router) {
    $router->get('',['middleware'=>[Auth::class],"uses"=>"DonationController@getAllDonations"]);
    $router->post('/campaigns/{campaignID}', ['middleware'=>Auth::class, "uses"=>"DonationController@createDonation"]);
    $router->get('/campaigns/{campaignID}', ['middleware'=>[Auth::class, CM::class], "uses"=>"DonationController@getDonationsByCampaignID"]);
    $router->patch('/{donationID}', ['middleware'=>[Auth::class], "uses"=>"DonationController@cancelDonationByID"]);
    $router->post('/{campaignID}/donations', ['middleware'=>Auth::class, "uses"=>"DonationController@createDonation"]);
    $router->get('/view/{id}',['middleware'=>[Auth::class], "uses"=>"DonationController@viewDonation"]);
});


