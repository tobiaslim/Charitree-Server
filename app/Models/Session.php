<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];
    protected $table = 'session';
    protected $primaryKey = 'session_id';
    public $timestamps = false;

    public function __construct(){
      do{
        $this->session_token = base64_encode(str_random(40));
      }while (Session::where('session_token', '=', $this->session_token)->exists());
      $now = new \DateTime();
      $this->session_expire = $now->modify('+1 day');
    }

    // Relationships
    public function user(){
      return $this->belongsTo('App\models\User');
    }
}
