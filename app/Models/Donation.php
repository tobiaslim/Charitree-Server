<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model {

    protected $fillable = [];


    public static $rules = [
        // Validation rules
    ];
    protected $table = 'Donation';
    protected $primaryKey = 'did';
    public $timestamps = false;

    public function __construct(){

    }

    /**
     * Relationship with other entity define hereafter
     */
    public function user(){
        return $this->belongsTo(User::class, 'User_id', 'id');
    }

    public function campaign(){
        return $this->belongsTo(Campaign::class, 'Campaign_id', 'id');
    }

    public function items(){
        return $this->belongsToMany(Item::class, 'Donation_has_Item', 'Donation_did', 'Item_id')->withPivot('qty');
    }

    public function address(){
        return $this->belongsTo(Address::class, 'Address_id', 'id');
    }
}
