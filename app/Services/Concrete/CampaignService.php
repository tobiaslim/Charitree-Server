<?php
namespace App\Services\Concrete;

use App\Services\Contracts\ICampaignService;
use App\Models\User;
use App\Models\Session;
use App\Contracts\IAuthenticate;
use App\Models\Campaign;
use App\Models\CampaignManager;


class CampaignService implements ICampaignService{

    public function __construct()
    {
    }

    
    public function create(array $array){
        $user = app(User::class);
        $cm = $user->campaignManager;
        $campaign = new Campaign();
        $campaign->name = $array['name'];
        $campaign->start_date = $array['start_date'];
        $campaign->end_date = $array['end_date'];
        $cm->campaigns()->save($campaign);

        $campaign->items()->sync($array['accepted_items']);
    }
    
    public function edit(array $array){
        
    }

    public function find($id){
        return Campaign::find($id);
    }
}