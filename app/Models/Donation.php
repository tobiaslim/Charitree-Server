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
        return $this->belongsTo(User::class);
    }

    public function campaign(){
        return $this->belongsTo(Campaign::class);
    }

    public function items(){
        return $this->belongsToMany(Item::class, 'Donation_has_Item', 'Donation_did', 'Item_id')->withPivot('qty');
    }
}
