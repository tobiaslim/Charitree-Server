<?php
namespace App\Services\Contracts;
use App\Models\User;

interface IUserService{
    public function create(array $array);
    public function edit(array $data);
    public function getUserByEmail(string $email);
}

