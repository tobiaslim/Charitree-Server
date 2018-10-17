<?php
namespace App\Contracts\Repository;
use App\Models\User;

interface IUserRepository extends IRepository{
    public function create(array $array);
    public function edit(array $data, User $user);
    public function getUserByEmail(string $email);
}

