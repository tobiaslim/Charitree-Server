<?php
namespace App\Services\Concrete;

use App\Models\User;
use App\Models\Address;
use App\Models\Session;
use App\Contracts\IAuthenticate;
use App\Models\Campaign;
use App\Models\CampaignManager;
use App\Services\Contracts\IDonationService;
use App\Models\Donation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;


class DonationService implements IDonationService{

    public function __construct()
    {
    }

    public function createDonation(array $array, User $user, $campaignID)
    {
        /**
         * Check if address belongs to user
         */
        $conditons = ["id"=>$array['address_id'], "user_id"=>$user->id];
        $address = Address::where($conditons)->first();
        if(is_null($address)){
           throw new ModelNotFoundException("Address does not belongs to you!");
        }

        /**
         * Check if campaign has expired or doest not exist
         */
        $campaign = Campaign::find($campaignID);
        $today = new Carbon;
        if($campaign == null || $campaign->end_date->lt(new Carbon)){
            throw new ModelNotFoundException("Campaign not found or it has ended.");
        }


        $donation = new Donation;
        $donation->status="pending";
        $donation->user()->associate($user);
        $donation->campaign()->associate($campaign);
        $donation->address()->associate($address);
        $donation->save();

        $donationsKey = $array['items']['keys'];
        $donationsQty = $array['items']['values'];

        $size = count($donationsKey);
        $items = array();
        for($i = 0; $i < $size; $i++){
            $items[$donationsKey[$i]]=['qty'=>$donationsQty[$i]];
        }
        $donation->items()->sync($items);
    }

    public function getAllDonations(User $user)
    {
        $donations;
        $items;
        $donationsResults = Donation::with(['items', 'campaign', 'address'])->where('User_id',$user->id)->get();
        if($donationsResults->isEmpty()){
            return NULL;
        }
        
        foreach($donationsResults as $donation){
            $items = array();

            foreach($donation->items as $item){
                $items[] = ['id'=>$item->id, 'name'=>$item->name, 'qty'=> $item->pivot->qty];
            }
            $donations[] = ["did"=> $donation->did, "status"=> $donation->status,
            "items"=>$items, "campaign"=>$donation->campaign, "pickup_address"=>$donation->address];
        }

        return $donations;
    }
}