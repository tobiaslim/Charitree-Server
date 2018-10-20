<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Contracts\Repository\IUserRepository;
use App\Models\CampaignManager;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;

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
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($this->users->create($request->all())){
            return response()->json(['status' => '1', 'message' => 'User created.'], Response::HTTP_CREATED);
        }
        else{
           return response()->json(['status' => '0', 'message' => 'Something went wrong']);
        }
    }

    public function editUser(Request $request, User $user){
        /**
         * How to find a better way of passing $user->id to the static array. 
         * Current work around.
         */
        $validator = Validator::make($request->all(), [
            'email'=>'required|email|unique:User,email,'.$user->id
        ]);

        if($validator->fails()){
            $errors = $validator->errors();
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($this->users->edit($request->all(), $user)){
            return response()->json(['status'=>'1','message'=>'User updated.'],201);
        }

        else{
            return response()->json(['status'=>'0','message'=>'Something went wrong']);
        }
    }

    public function registerAsCampaignManager(Request $request, User $user){

        $validator = Validator::make($request->all(), CampaignManager::$rules['register']);
        
        if($validator->fails()){
            $errors = $validator->errors();
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if(!is_null($user->campaignManager)){
            return response()->json(['status'=>'0', 'message'=>'Already a camopaign manger'], Response::HTTP_CONFLICT);
        }


        $cm = new CampaignManager($request->all());
        $cm->cid = $user->id;
        $user->campaignManager()->save($cm);
        $user->refresh();
        return response()->json(['status' => '1', 'message' => 'Campaign Manager Created'], Response::HTTP_CREATED);

    }

    public function getCurrentCampaignManagerDetails(User $user){
        $cm = $user->campaignManager;
        return response()->json(['status' => '1', 'campaign_manager' => $cm], Response::HTTP_OK);
    }
}
