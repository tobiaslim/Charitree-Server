<?php
namespace App\Services\Contracts;

interface ICampaignService{
    public function find(int $id);
    public function create(array $array);
}

