<?php 
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Campaign;
use App\Models\User;
use App\Models\Address;
use App\Rules\ArraySameSizeAs;
use App\Services\Contracts\ICampaignService;
use App\Services\Contracts\IDonationService;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DonationController extends Controller
{
    protected $donationService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(IDonationService $donationService)
    {
        $this->donationService = $donationService;
    }

    public function getAllDonations(Request $request, User $user)
    {
        $donations = $this->donationService->getAllDonations($user);
        if (!is_null($donations)) {
            return response()->json(['status' => 1, 'message' => "Donations of a user.", "donations" => $donations], Response::HTTP_OK);
        } else {
            return response()->json(['status' => 0, 'errors' => ["message" => 'No donations found.'], "donations" => null], Response::HTTP_NOT_FOUND);
        }
    }

    public function createDonation(Request $request, User $user, ICampaignService $campaignService, $campaignID){
        $validator = Validator::make($request->all(),[
            'address_id'=>'required|integer|exists:Address,id',
            'items'=>'required',
            'items.keys'=>['required','array', new ArraySameSizeAs],
            'items.values'=>'required|array',
            'items.keys.*'=>'required|integer|between:1,7',
            'items.values.*'=>'integer',
            'pickup_date'=>'required',
            'pickup_time'=>'required'
        ]);

        if($validator->fails()){
            $errors = $validator->errors()->toArray();
            $errors['message'] = "Unproccessable request";
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $this->donationService->createDonation($request->all(), $user, $campaignID);
        }catch(ModelNotFoundException $e){
            return response()->json(["status"=>"0", "errors"=>['message'=>$e->getMessage()]],Response::HTTP_NOT_FOUND);
        }
        return response()->json(["status"=>"1", "message"=> "Donation added."], Response::HTTP_CREATED);
    }


    public function cancelDonationByID($donationID){

        try{
            $this->donationService->cancelDonation($donationID);
        }catch(ModelNotFoundException $e){
            $errors = ["message"=> $e->getMessage()];
            return response()->json(['status'=>0, 'errors'=>$errors]);
        }
        return response()->json(['status'=>1, 'message'=>'Donation cancelled']);
    }
    
    public function viewDonation(Request $request, User $user, $donationID)
    {
        //validator only used when the request body is not null

        try{
            $donation=$this->donationService->viewDonation($user,$donationID); //do not need to execute this twice
        }catch(ModelNotFoundException $e){
            return  response()->json(["status"=>"0", "errors"=>['message'=>$e->getMessage()]],Response::HTTP_NOT_FOUND);
        } //the expection is defined inside donationService
      
        return response()->json(["status"=>"1","message"=>"Donation returned","donation"=>$donation],Response::HTTP_OK);
    }

    public function getAllDonationsByCampaignID(Request $request, User $user, $id){
        $donations;
        try{
            $donations = $this->donationService->getAllDonationsByCampaignID($user,$id);
        }
        catch(ModelNotFoundException $e){
            $errors['message'] = $e->getMessage();
            return response()->json(['status'=>0, 'errors'=>$errors], Response::HTTP_NOT_FOUND);
        }
        if(is_null($donations)){
            $errors['message'] = "No donations found.";
            return response()->json(['status'=>0, 'errors'=>$errors], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['status'=>1, 'donations'=>$donations,'message'=>"List of donations for campaign id $id"], Response::HTTP_OK);
    }

    public function getDonationByDonationID(User $user,$id){
        $donation = $this->donationService->getDonationByDonationID($user, $id);
        if(is_null($donation)){
            $errors['message'] = "Donation id $id not found.";
            return response()->json(['status'=>0, 'errors'=>$errors], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['status'=>1, 'donation'=>$donation], Response::HTTP_OK);
    }
}