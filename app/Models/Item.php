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
}
