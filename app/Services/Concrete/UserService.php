<?php
namespace App\Services\Concrete;

use App\Services\Contracts\IUserService;
use App\Models\User;
use App\Models\Session;
use App\Services\Contracts\IAuthenticate;
use App\Models\Address;


class UserService implements IUserService, IAuthenticate{

    public function __construct()
    {
    }

    /**
     * Create a user based on the input array of information
     * 
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

    /**
     * 
     * @param array data,  data to edit
     * @param int id the user id of the data to be edited
     * 
     */

    public function edit(array $data){
        $user = app(User::class);
        $user->email=$data["email"];
        $user->last_name=$data["last_name"];
        $user->first_name=$data["first_name"];

        if($user->save()){
            return true;
        }

        else{
            return false;
        }
    }

    /**
     * Retrieve a user based on email. Return null if not found.
     * 
     * @param array $array Fills to be filled into user when creating.
     * @return User
     * 
     */
    public function getUserByEmail(string $email){
        return User::where('email', $email)->first();
    }

    public function createNewSessionForUser(User $user){
        $session = new Session();
        $user->session()->save($session);
        return $session->session_token;;
    }

    /**
     * Authenticate a user and create a session
     * Return session token if valid, else return null
     */

    public function login(array $credentials){
        $user = $this->getUserByEmail($credentials['email']);
        if($user == null){
            return null;
        }

        if($user->validatePassword($credentials['password'])){
            return $this->createNewSessionForUser($user);
        }
        else{
            return null;
        }
    }

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
}