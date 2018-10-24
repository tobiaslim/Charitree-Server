<?php
namespace App\Services\Contracts;

use App\Models\Campaign;
use App\Models\User;
use App\Models\Address;


interface IDonationService{
    public function createDonation(array $array, User $user, int $campaign);
}

