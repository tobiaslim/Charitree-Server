<?php
namespace App\Services\Concrete;

use App\Services\Contracts\ICampaignService;
use App\Models\User;
use App\Models\Session;
use App\Contracts\IAuthenticate;
use App\Models\Campaign;
use App\Models\CampaignManager;
use Symfony\Component\Finder\Comparator\DateComparator;
use Illuminate\Support\Carbon;
use App\Models\DonationStatus;
use App\Utility\IHttpClient;


class CampaignService implements ICampaignService{

    public function __construct()
    {
    }

    
    public function create(array $array){
        $user = app(User::class);
        $cm = $user->campaignManager;
        $campaign = new Campaign($array);

        $cm->campaigns()->save($campaign);

        $campaign->items()->sync($array['accepted_items']);
    }
    
    public function edit(array $array){
        
    }

    public function find($id){
        return Campaign::find($id);
    }

    public function getAllCampaigns($max){
        $campaigns;
        $today = new Carbon();
        $todayString = $today->toDateString();
        if(is_int($max)){ 
            $campaigns = Campaign::with(['campaignmanager.user'])->where('end_date', '>=', $todayString)->orderBy('end_date', 'asc')->take($max)->get();
        }
        else{
            $campaigns = Campaign::with(['campaignmanager.user'])->where('end_date', '>=', $todayString)->orderBy('end_date', 'asc')->get();
        }

        if($campaigns->isEmpty()){
            return null;
        }

        $httpClient = app(IHttpClient::class);
        $yesterday = Carbon::yesterday();
        $yesterdayString = $yesterday->toDateString();
        $params = ['date'=>$yesterdayString];
        $httpClient->request('GET', 'https://api.data.gov.sg/v1/environment/4-day-weather-forecast', $params);
        $response = $httpClient->getResponseBody();
        $itemsCount = count($response['items']);
        $forecastRaw = $response['items'][$itemsCount-1]['forecasts'];
        
        $forecast = array();

        foreach($forecastRaw as $fc){
            $date = Carbon::parse($fc['date']); 
            $forecast[$date->toDateString()]=$fc['forecast']; 
        }
        $accepted_items;
        $campaignManagerInfo;
        $i = 0;
        //Retrieve the items relationship infomration. 
        foreach($campaigns as $campaign){
            $campaignManagerInfo[$i] = $campaign->campaignManager->user->first_name . " " . $campaign->campaignManager->user->last_name;
            $items = $campaign->items()->get();
            foreach($items as $item){
                $val = ['key'=>$item->id, 'value'=>$item->name];  
                $accepted_items[$i][] =$val; 
            }
            $i++;
        }
        
        // Response structure clean up
        // Rename campaigns['campaignmanager'] to campaigns['campaign_manager']
        // Load the campaign manager info, and accepted items
        // remove unneccessary information.
        $campaigns = $campaigns->toArray();
        $i = 0;
        foreach($campaigns as $campaign){
            $campaigns[$i]["campaign_manager"] = $campaigns[$i]["campaignmanager"];
            unset($campaigns[$i]["campaignmanager"]);
            $campaigns[$i]["campaign_manager"]["name"] = $campaignManagerInfo[$i];
            unset($campaigns[$i]["campaign_manager"]["user"]);
            $campaigns[$i]["accepted_items"] = $accepted_items[$i];

            $startDate = Carbon::make($campaign['start_date']);
            $endDate = Carbon::make($campaign['end_date']);
            foreach($forecast as $key => $val){
                $date = Carbon::make($key);
                if($date->between($startDate, $endDate)){
                    // forecasted date falls within the campaign. attached to response
                    $campaigns[$i]["weather_forecasts"][] = ['date'=>$key, 'forecast' => $val];
                }
            }
            $daysLeft = $today->diffInDays($startDate);
            $campaigns[$i]["days_left"] = $daysLeft;

            $i++;
        }

        return $campaigns;
    }

    public function getCampaignByCampaignID($id){
        $campaign = Campaign::with(['items','campaignmanager.user'])->where('id',$id)->first();

        if(is_null($campaign)){
            return null;
        }

        $campaign = $campaign->toArray();
        $campaign_manager = array();
        foreach($campaign['campaignmanager']['user'] as $key => $value){
            $campaign_manager[$key] = $value;
        }
        $accepted_items = array();
        foreach($campaign['items'] as $item){
            $accepted_items[] = ['key'=>$item['id'], 'value'=>$item['name']]; 
        }
        unset($campaign['cid']);
        unset($campaign['items']);
        $campaign['accepted_items'] = $accepted_items;
        $campaign['campaign_manager'] = $campaign_manager;
        unset($campaign['campaignmanager']);

        return $campaign;
    }


    public function getAllCampaignBySession(User $user){
        $cid=$user->campaignManager->cid;
        $today = new Carbon();
        $campaigns = Campaign::with(['campaignmanager.user'])->withCount(['donations as total_donations','donations as pending_donations'=>function($query){
            $query->where('status', DonationStatus::PENDING);
        }, 'donations as inprogress_donations'=>function($query){
            $query->where('status', DonationStatus::INPROGRESS);
        }])->where('cid', $cid)->orderBy('end_date', 'asc')->get();
        $campaigns;
        $today = new Carbon();
        $todayString = $today->toDateString();
   

        if($campaigns->isEmpty()){
            return null;
        }

        $httpClient = app(IHttpClient::class);
        $yesterday = Carbon::yesterday();
        $yesterdayString = $yesterday->toDateString();
        $params = ['date'=>$yesterdayString];
        $httpClient->request('GET', 'https://api.data.gov.sg/v1/environment/4-day-weather-forecast', $params);
        $response = $httpClient->getResponseBody();
        $itemsCount = count($response['items']);
        $forecastRaw = $response['items'][$itemsCount-1]['forecasts'];
        
        $forecast = array();

        foreach($forecastRaw as $fc){
            $date = Carbon::parse($fc['date']); 
            $forecast[$date->toDateString()]=$fc['forecast']; 
        }

        $accepted_items;
        $campaignManagerInfo;
        $i = 0;
        //Retrieve the items relationship infomration. 
        foreach($campaigns as $campaign){
            $campaignManagerInfo[$i] = $campaign->campaignManager->user->first_name . " " . $campaign->campaignManager->user->last_name;
            $items = $campaign->items()->get();
            foreach($items as $item){
                $val = ['key'=>$item->id, 'value'=>$item->name];  
                $accepted_items[$i][] =$val; 
            }
            $i++;
        }
        
        // Response structure clean up
        // Rename campaigns['campaignmanager'] to campaigns['campaign_manager']
        // Load the campaign manager info, and accepted items
        // remove unneccessary information.
        $campaigns = $campaigns->toArray();
        $i = 0;
        foreach($campaigns as $campaign){
            $campaigns[$i]["campaign_manager"] = $campaigns[$i]["campaignmanager"];
            unset($campaigns[$i]["campaignmanager"]);
            $campaigns[$i]["campaign_manager"]["name"] = $campaignManagerInfo[$i];
            unset($campaigns[$i]["campaign_manager"]["user"]);
            $campaigns[$i]["accepted_items"] = $accepted_items[$i];

            $startDate = Carbon::make($campaign['start_date']);
            $daysLeft = $today->diffInDays($startDate);
            $campaigns[$i]["days_left"] = $daysLeft;

            $endDate = Carbon::make($campaign['end_date']);
            foreach($forecast as $key => $val){
                $date = Carbon::make($key);
                if($date->between($startDate, $endDate)){
                    // forecasted date falls within the campaign. attached to response
                    $campaigns[$i]["weather_forecasts"][] = ['date'=>$key, 'forecast' => $val];
                }
            }

            $i++;
        }

        return $campaigns;
    }
}