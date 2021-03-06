<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model 
{
    /*
     * Table specific information
     */
    protected $table = 'Campaign'; //case sensitive with database in some environment
    protected $primarykey = 'id'; //eloquent assume primarykey is use as id, if not declare the column name of the primary key
    public $timestamps = false;

    /**
     * 
     * Specify which are dates columns to be auto converted to Carbon/Carbon instance when created
     */
    protected $dates = [
        'start_date',
        'end_date'
    ];

    /**
     * Specify the date format being stored in database as well as returninng of values
     */
    protected $dateFormat = 'Y-m-d';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','start_date','end_date','start_time', 'end_time', 'description', 'collection_point', 'postal_code'
    ];

    public static $rules = [
        'create'=>[
            'name'=>'required',
            'accepted_items'=>'required|array',
            'accepted_items.*'=>'required|integer|between:1,7',
            'start_date'=>'required|date|date_format:Y-m-d|after:today',
            'end_date'=>'required|date|date_format:Y-m-d|after:start_date',
            'start_time'=>'required|int|between:0,23',
            'end_time'=>'required|int|between:0,24|gte:start_time',
            'description'=>'required|max:255',
            'collection_point'=>'required|max:45|regex:/^[\w\s]+$/',
            'postal_code'=>'required|size:6'
        ],
        'get'=>[
            'max'=>"sometimes|required|integer"
        ]
    ];

    /**
     * Relationship with other entity define hereafter
     */
    public function items(){
        return $this->belongsToMany(Item::class, 'Campaign_has_Item', 'Campaign_id', 'Item_id');
    }

    public function campaignManager(){
        return $this->belongsTo(CampaignManager::class, 'cid','cid');
    }

    public function donations(){
        return $this->hasMany(Donation::class, 'Campaign_id', 'id');
    }
}
