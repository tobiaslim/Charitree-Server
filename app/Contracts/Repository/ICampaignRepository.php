<?php
namespace App\Contracts\Repository;
use App\Models\User;
use App\Models\Campaign;
use App\Models\CampaignManager;

interface ICampaignRepository extends IRepository{
    public function create(array $array);
    public function edit(array $data);
}

