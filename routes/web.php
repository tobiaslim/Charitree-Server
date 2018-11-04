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
 * POST     /users                                          Creating a new user
 * POST     /users/campaignmanager                          Create campaign manager from a user
 * GET      /users/campaignmanager                          Get current session campaign manager details 
 * PUT      /users                                          Edit user
 */
$router->group(['prefix' => 'users'], function () use ($router) {
    $router->post('', 'UserController@register');
    $router->post('/campaignmanagers',['middleware'=>[Auth::class], "uses"=>"UserController@registerAsCampaignManager"]);
    $router->get('/campaignmanagers',['middleware'=>[Auth::class, CM::class], "uses"=>"UserController@getCurrentCampaignManagerDetails"]); 
    $router->put('', ['middleware'=>[Auth::class], "uses"=>"UserController@editUser"]);
});

/**
 * Routes:
 * POST     /addresses                                      Create address for a user 
 * GET      /addresses                                      Get all addresses for a user
 */
$router->group(['prefix' => 'addresses'], function () use ($router) {
    $router->post('',['middleware'=>[Auth::class],"uses"=>"AddressController@addAddresses"]);                 
    $router->get('',['middleware'=>[Auth::class],"uses"=>"AddressController@getAddressByUser"]);
});

/**
 * Routes:
 * POST     /sessions                                       Create a new session
 * GET      /sessions                                       Check if a session is valid using auth 
 */
$router->group(['prefix' => 'sessions'], function () use ($router) {
    $router->post('', 'SessionController@createSession');
    $router->get('',['middleware'=>[Auth::class], "uses"=>"SessionController@testauthorization"]);
});

/**
 * Routes:
 * GET      /items                                          Get list of items
 * GET      /uen                                            Get organization name by UEN
 */
$router->get('/items', "ItemController@getItems");
$router->get('/uen', "UserController@retrieveOrganizationNameByUEN");


/**
 * Routes:
 * GET      /campaigns                                      Get all campaigns by user
 * POST     /campaigns                                      Create campaign
 * GET      /cmapaigns/{campaignID}                         Get a campaign by id
 * GET      /campaign/campaignmanagers                      Get all campaigns belonging to a CM by CM session
 */
$router->group(['prefix' => 'campaigns'], function () use ($router) {
    $router->get('', "CampaignController@getCampaigns");
    $router->post('', ['middleware'=>[Auth::class, CM::class], "uses"=>"CampaignController@createCampaign"]);
    $router->get('/{id:[0-9]+}', "CampaignController@getCampaignByCampaignID");
    $router->get('/campaignmanagers',['middleware'=>[Auth::class, CM::class],"uses"=>"CampaignController@getAllCampaignBySession"]);
});

/**
 * Routes:
 * GET      /donations/campaignmanagers/campaigns/{id}      Get all donations for a campaign ID that belongs to a CM
 * GET      /donations/{id}/campaignmanagers/               Get a donations by donation ID for campaign manager
 * GET      /donations                                      Get all donations of the user based on the user's session
 * POST     /donations/campaigns/{campaignID}               Create donation for a campaign ID.
 * GET      /donations/campaigns/{campaignID}               Get all donations for a campaign by campaign id
 * PATCH    /donations/{donationID}                         Cancel Donation
 * GET      /donations/{donationID}                         View a specific donation
 * PATCH    /donations/{donationID}/campaignmanagers        Change the donation status for a donationID by a campaign manager
 * 
 */
$router->group(['prefix' => 'donations'], function () use ($router) {
    $router->get('/campaignmanagers/campaigns/{id:[0-9]+}',['middleware'=>[Auth::class, CM::class],"uses"=>"DonationController@getAllDonationsByCampaignID"]);
    $router->get('/{id:[0-9]+}/campaignmanagers',['middleware'=>[Auth::class, CM::class],"uses"=>"DonationController@getDonationByDonationID"]);
    $router->get('',['middleware'=>[Auth::class],"uses"=>"DonationController@getAllDonations"]);
    $router->post('/campaigns/{campaignID:[0-9]+}', ['middleware'=>Auth::class, "uses"=>"DonationController@createDonation"]);
    $router->get('/campaigns/{campaignID:[0-9]+}', ['middleware'=>[Auth::class, CM::class], "uses"=>"DonationController@getDonationsByCampaignID"]);
    $router->patch('/{donationID:[0-9]+}', ['middleware'=>[Auth::class], "uses"=>"DonationController@cancelDonationByID"]);
    $router->get('/{id:[0-9]+}',['middleware'=>[Auth::class], "uses"=>"DonationController@viewDonation"]);
    $router->patch('/{id:[0-9]+}/campaignmanagers',['middleware'=>[Auth::class, CM::class],"uses"=>"DonationController@changeStatusOfDonation"]);
});


