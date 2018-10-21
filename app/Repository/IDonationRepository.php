<?php
namespace App\Contracts\Repository;

use App\Models\Campaign;
use App\Models\User;


interface IDonationRepository extends IRepository{
    public function createDonation(array $array, User $user, Campaign $campaign);
}

