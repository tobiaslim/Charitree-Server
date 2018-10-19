<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {

    protected $fillable = [];


    public static $rules = [
        // Validation rules
    ];
    protected $table = 'Item';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function __construct(){

    }

    /**
     * Relationship with other entity define hereafter
     */
    public function campaigns(){
        return $this->belongsToMany(Campaign::class, 'Campaign_has_Item', 'Item_id', 'Campaign_id');
    }

    public function donation(){
        return $this->belongsToMany(Donation::class, 'Donation_has_Item', 'Item_id', "Donation_did");
    }
}
