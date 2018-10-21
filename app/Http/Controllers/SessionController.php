<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Contracts\Repository\IUserRepository;
use Symfony\Component\HttpFoundation\Response;


class SessionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */ 
    public function __construct()
    {
    }

    public function createSession(Request $request, IUserRepository $users)
    {   
        $validator = Validator::make($request->all(), User::$rules['login']);

        if($validator->fails()){
            $errors = $validator->errors()->toArray();
            $errors["message"] = "Unable to process parameters.";
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $session_token = $users->login($request->all());

        if (!is_null($session_token)) {
            return response()->json(['status' => '1', 'user_token' =>$session_token], Response::HTTP_CREATED);
        } else {
            return response()->json(['status' => '0', "errors"=>['message'=>"Email and password pair not found."]], Response::HTTP_NOT_FOUND);
        }
    }

    public function testauthorization(Request $request){
        return response()->json(['status' => '1', "message"=>"If you see this, you pass the authorization"], Response::HTTP_OK);
    }
}
