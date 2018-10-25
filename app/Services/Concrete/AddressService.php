<?php
namespace App\Services\Concrete;
use App\Services\Contracts\IAddressService;
use App\Models\User;
use App\Models\Address;

class AddressService implements IAddressService{

    public function addUserAddress($addresses, User $user){
        $addressArray;
        $i = 0;
        /**
         * Create address instances
         */
        foreach($addresses as $add){
            $addressArray[$i] = new Address();
            $addressArray[$i]->fill($add);
            $i++;
        }
        /**
         * save to database
         */
        $user->address()->saveMany($addressArray);

        /**
         * Refreshing the model to retrieve the address id into memory from database.
         */
        foreach($addressArray as $address){
            $address->refresh();
        }
        return $addressArray;
    }

    public function getUserAddresses(User $user){
        if(is_null($user->address) || count($user->address)==0){
            return null;
        }
        return $user->address;
    }
}