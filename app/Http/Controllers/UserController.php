<?php
namespace App\Http\Controllers;

use App\Models\User;
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

    public function editUser(Request $request){
        
        if($this->users->edit($request->all(),$request->get_current_user)){
            return response()->json(['status'=>'1','message'=>'User updated.'],201);
        }

        else{
            return response()->json(['status'=>'0','message'=>'Something went wrong']);
        }
    }

    public function registerAsCampaignManager(Request $request){
        
    }
}
