<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Contracts\Repository\IUserRepository;

class UserController extends Controller
{
    protected $users; //IUserRespository type
    /**
     * Create a new controller instance.
     *
     * @return void
     */ 
    public function __construct(IUserRepository $users)
    {
        $this->users = $users;
    }

    public function register(Request $request)
    {   
        $validator = Validator::make($request->all(), User::$rules['register']);
        
        if($validator->fails()){
            $errors = $validator->errors();
            return response()->json(["status"=>"0", "errors"=> $errors], 422);
        }

        if($this->users->create($request->all())){
            return response()->json(['status' => '1', 'message' => 'User created.'], 201);
        }
        else{
           return response()->json(['status' => '0', 'message' => 'Something went wrong']);
        }
    }

    public function authenticate(Request $request)
    {   
        $validator = Validator::make($request->all(), User::$rules['login']);

        if($validator->fails()){
            $errors = $validator->errors();
            return response()->json(["status"=>"0", "errors"=> $errors], 422);
        }

        $user = User::where('email', $request->input('email'))->first();
        if ($user == null) {
            return response()->json(['status' => '0'], 401);
        }
        if ($user->validatePassword($request->input('password'))) {
            $session = new Session();
            $user->session()->save($session);
            return response()->json(['status' => '1', 'user_token' => $session->session_token]);
        } else {
            return response()->json(['status' => '0'], 401);
        }
    }
}
