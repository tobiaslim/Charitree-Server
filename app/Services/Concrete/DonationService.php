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

    public function createDonation(array $array, User $user, int $campaignID)
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

        $donation = new Donation;
        $donation->pickup_date=$date;
        $donation->pickup_time=$time;
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
        
        $i = 0;
        $items = array();
        foreach($donationsResults as $donation){
            foreach($donation->items as $item){
                $items[$i][] = ['id'=>$item->id, 'name'=>$item->name, 'qty'=> $item->pivot->qty];
            }
            $i++;
        }
        $donationsResults->toArray();
        $i = 0;
        foreach($donationsResults as $donation ){
            unset($donationsResults[$i]['items']);
            $donationsResults[$i]['items'] = $items[$i];
            $i++;
        }

        return $donationsResults;
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

       return $donation;
    }

    public function getAllDonationsByCampaignID(User $user, $campaignID){
        $cmid = $user->campaignManager->cid;
        $campaigns = Campaign::with(['donations.items','donations.address','donations.user'])->where('cid',$cmid)->where('id',$campaignID)->get();

        if($campaigns->isEmpty()){
            throw new ModelNotFoundException("No such campaign or campain is invalid.");
        }

        $donations = $campaigns->first()->donations;
        if(count($donations) == 0){
            return null;
        }

        //$donations = $donations->toArray();
        $items = array();
        $i = 0;
        foreach($donations as $donation){
            foreach($donation->items as $item){
                $items[$i][] = ["id"=>$item->id, "name"=>$item->name, "qty"=>$item->pivot->qty];
            }
            $i++;
        }

        $donations = $donations->toArray();
        $i = 0;
        foreach($donations as $donation){
            unset($donations[$i]['items']);
            $donations[$i]['items'] = $items[$i];
            $i++;
        }

        return $donations;
    }

    public function getDonationByDonationID(User $user, $donationID){
        $cid = $user->campaignManager->cid;
        $donation = Donation::with(['user','items','address'])->where('did', $donationID)->whereHas('campaign', function($query) use ($cid){
            $query->where('cid', $cid);
        })->get();

        if($donation->isEmpty()){
            return null;
        }
        $donation = $donation->first();
        $items = array();
        $i = 0;
        foreach($donation->items as $item){
            $items[$i]['id'] = $item->id;
            $items[$i]['name'] = $item->name;
            $items[$i]['qty'] = $item->pivot->qty;
            $i++;
        }
        $donation = $donation->toArray();

        unset($donation['items']);
        $donation['items'] = $items;
        
        return $donation;
    }
    
    public function approveDonation(User $user, $donationID){
        $cid = $user->campaignManager->cid;
        $donation = Donation::where('did', $donationID)->where('status', DonationStatus::PENDING)->whereHas('campaign', function($query) use ($cid){
            $query->where('cid', $cid);
        })->first();

        if(is_null($donation)){
            throw new ModelNotFoundException("Either donation is already approved, or it does not exist as your donations.");
        }

        $donation->status = DonationStatus::APPROVED;
        $donation->save();
    }

    public function rejectDonation(User $user, $donationID){
        $cid = $user->campaignManager->cid;
        $donation = Donation::where('did', $donationID)->where('status', DonationStatus::PENDING)->whereHas('campaign', function($query) use ($cid){
            $query->where('cid', $cid);
        })->first();

        if(is_null($donation)){
            throw new ModelNotFoundException("Either donation is already rejected or in-progress, or it does not exist as your donations.");
        }

        $donation->status = DonationStatus::REJECTED;
        $donation->save();
    }

    public function assignVolunteerToDonation(User $user, $donationID, $volunteer){
        $cid = $user->campaignManager->cid;
        $donation = Donation::where('did', $donationID)->where('status', DonationStatus::APPROVED)->whereHas('campaign', function($query) use ($cid){
            $query->where('cid', $cid);
        })->first();

        if(is_null($donation)){
            throw new ModelNotFoundException("Either donation is already in-progress, or it does not exist as your donations.");
        }

        $donation->status = DonationStatus::INPROGRESS;
        $donation->volunteer_name = $volunteer['volunteer_name'];
        $donation->volunteer_HP = $volunteer['volunteer_HP'];
        $donation->save();
    }

    public function completeDonation(User $user, $donationID){
        $cid = $user->campaignManager->cid;
        $donation = Donation::where('did', $donationID)->where('status', DonationStatus::INPROGRESS)->whereHas('campaign', function($query) use ($cid){
            $query->where('cid', $cid);
        })->first();

        if(is_null($donation)){
            throw new ModelNotFoundException("Either donation is already completed, or it does not exist as your donations.");
        }

        $donation->status = DonationStatus::COMPLETED;
        $donation->save();
    }

    public function cancelDonationByCampaignManager(User $user, $donationID){
        $cid = $user->campaignManager->cid;

        $donation = Donation::where('did', $donationID)
        ->where(function ($query){
            $query->where('status', DonationStatus::INPROGRESS)
            ->orWhere('status',DonationStatus::PENDING)
            ->orWhere('status',DonationStatus::APPROVED);
        })
        ->whereHas('campaign', function($query) use ($cid){
            $query->where('cid', $cid);
        })->first();

        if(is_null($donation)){
            throw new ModelNotFoundException("Either donation is already completed or cancelled, or it does not exist as your donations.");
        }

        $donation->status = DonationStatus::CANCELLED;
        $donation->save();
    }

    public function getDonationsCount(User $user, String $countBy){
        return Donation::where('status', $countBy)->where('User_id', $user->id)->count();
    }
}
