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
            $errors = $validator->errors();
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $users->getUserByEmail($request->input('email'));

        if ($user == null) {
            return response()->json(['status' => '0'], 401);
        }
        if ($user->validatePassword($request->input('password'))) {
            $session = $users->createNewSessionForUser($user);
            return response()->json(['status' => '1', 'user_token' => $session->session_token], Response::HTTP_CREATED);
        } else {
            return response()->json(['status' => '0'], 401);
        }
    }

    public function testauthorization(Request $request){
        return response()->json(['status' => '1', "message"=>"If you see this, you pass the authorization"], Response::HTTP_OK);
    }
}
