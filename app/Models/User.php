<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use App\Models\Address;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /*
     * Table specific information
     */
    protected $table = 'User'; //case sensitive with database in some environment
    //protected $primarykey = ''; //eloquent assume primarykey is use as id, if not declare the column name of the primary key
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'first_name', 'last_name'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public static $rules = [
        "register" => [
            'email' => 'required|email|unique:User'
        ],
        "login"=>[
            'email' => 'required|email',
            'password'=>'required'
        ],
        //this rule not in used.
        "edit"=>[
            'first_name'=>'required|string|alpha',
            'last_name'=>'required|string|alpha'
        ]
    ];


    /**
     * The plain text password. Will be stored as a hash.
     *
     * @param string
     */
    public function storePassword(string $password)
    {
        $this->password = Hash::make($password);
    }


    /**
     * The plain text password. Will be hashed and check if correct.
     *
     * @param string
     * @return boolean 
     */
    public function validatePassword(string $password)
    {
        if (Hash::check($password, $this->password)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Relationship with other entity define hereafter
     */

    public function session()
    {
        return $this->hasMany('App\Models\Session');
    }

    public function campaignManager(){
        return $this->hasOne('App\Models\CampaignManager', 'cid');
    }

    public function donations(){
        return $this->hasMany(Donation::class);
    }

    public function address(){
        return $this->hasMany(Address::class, 'user_id', 'id');
    }

}
