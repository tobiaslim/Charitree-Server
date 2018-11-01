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
use App\Models\DonationStatus;


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

        $date=$array['pickup_date'];
        $time=$array['pickup_time'];
        $dateTime=$date.",".$time;
        
        $donation = new Donation;
        $donation->pickup_datetime=$dateTime;
        $donation->status= DonationStatus::PENDING;
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

    public function cancelDonation($donationID){
        $user = app(User::class);
        $conditons = ['did'=>$donationID, 'User_id'=>$user->id];
        $donation = Donation::where($conditons)->first();
        if(is_null($donation) || $donation->status == DonationStatus::CANCELLED || $donation->status == DonationStatus::COMPLETED){
            throw new ModelNotFoundException("Donation not found!");
        }

        $donation->status = DonationStatus::CANCELLED;
        $donation->save();
    }
    public function viewDonation(User $user,$donationID)
    {
        $donation=Donation::with(['address','campaign'])->where(['User_id'=>$user->id,'did'=>$donationID])->first();
        //use an associative string to define the conditions
        if(is_null($donation)){
            throw new ModelNotFoundException("This user does not have this donation ID.");
        }
        $donation = $donation->toArray(); 
        $datetime;
        if(isset($donation['pickup_datetime'])){
            $datetime = explode(',',$donation['pickup_datetime']);
            $donation['pickup_date'] = $datetime[0];
            $donation['pickup_time'] = $datetime[1];
        }
        unset($donation['pickup_datetime']);
        $donation['pickup_address'] = $donation['address'];
        unset($donation['address']);

       return $donation;
    }
}