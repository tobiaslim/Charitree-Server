<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Session;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:user',
        ]);

        $user = new User($request->all());
        $user->storePassword($request->input("password"));

        if ($user->save()) {
            return response()->json(['status' => '1', 'message' => 'User created.']);
        } else {
            return response()->json(['status' => '0', 'message' => 'Something went wrong']);
        }
    }

    public function authenticate(Request $request)
    {
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
