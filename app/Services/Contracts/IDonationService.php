<?php
namespace App\Services\Contracts;

use App\Models\Campaign;
use App\Models\User;


interface IDonationService{
    public function createDonation(array $array, User $user, Campaign $campaign);
}

