<?php
namespace App\Contracts\Repository;

interface IUserRepository extends IRepository{
    public function create(array $array);
    public function edit(array $data, int $id);
}

