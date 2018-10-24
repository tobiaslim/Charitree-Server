<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model {

    protected $fillable = ['street_name', 'unit', 'zip'];

    protected $dates = [];

    public static $rules = [
        // Validation rules
        "create"=>[
            'addresses'=>'required',
            'addresses.*.street_name'=>'required|max:45|regex:/^[\w\s]+$/',
            'addresses.*.unit'=>'sometimes|nullable|max:10',
            'addresses.*.zip'=>'required|size:6'
        ]
    ];
    protected $table = 'Address';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function __construct(){
    }

    // Relationships
    public function user(){
      return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
