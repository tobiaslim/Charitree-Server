<?php
namespace App\Services\Concrete;

use App\Models\User;
use App\Models\Session;
use App\Contracts\IAuthenticate;
use App\Models\Campaign;
use App\Models\CampaignManager;
use App\Services\Contracts\IDonationService;
use App\Models\Donation;


class DonationService implements IDonationService{

    public function __construct()
    {
    }

    public function createDonation(array $array, User $user, Campaign $campaign)
    {
        $donation = new Donation;
        $donation->status="pending";
        $donation->user()->associate($user);
        $donation->campaign()->associate($campaign);

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
}