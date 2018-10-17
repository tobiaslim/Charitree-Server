<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignManager extends Model 
{
    /*
     * Table specific information
     */
    protected $table = 'CampaignManager'; //case sensitive with database in some environment
    protected $primarykey = 'cid'; //eloquent assume primarykey is use as id, if not declare the column name of the primary key
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'UEN', 'organization_name'
    ];

    public static $rules = [
        'register'=>[
            'UEN'=>'required|between:9,10',
            'organization_name'=>'required'
        ]
    ];

    public function user(){
        return $this->hasOne('App\Models\User');
    }
}
