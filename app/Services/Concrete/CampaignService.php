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

            $i++;
        }

        return $campaigns;
    }
}