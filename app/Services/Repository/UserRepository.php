<?php
namespace App\Services\Repository;

use App\Contracts\Repository\IUserRepository;
use App\Models\User;


class UserRepository implements IUserRepository{

    public function __construct()
    {
    }

    /*
     * @param array $array Fills to be filled into user when creating.
     * @return boolean
     * 
     */
    public function create(array $array)
    {
        $user = new User($array);
        $user->storePassword($array["password"]);
        
        if($user->save()){
            return true;
        }
        else{
            return false;
        }
    }

    public function edit(array $data, int $id){

    }
}