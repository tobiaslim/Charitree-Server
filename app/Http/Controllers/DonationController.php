<?php 
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Campaign;
use App\Models\User;
use App\Rules\ArraySameSizeAs;
use Illuminate\Support\Carbon;
use App\Services\Contracts\ICampaignService;
use App\Services\Contracts\IDonationService;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;

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

    public function createDonation(Request $request, User $user, ICampaignService $campaignService, $id){
        $validator = Validator::make($request->all(),[
            'items.keys'=>[new ArraySameSizeAs],
            'items.keys.*'=>'required|integer|between:1,7',
            'items.values.*'=>'integer'
        ]);

        if($validator->fails()){
            $errors = $validator->errors()->toArray();
            $errors['message'] = "Unproccessable request";
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $campaign = $campaignService->find($id);
        $today = new Carbon;
        if($campaign == null || $campaign->end_date->lt(new Carbon)){
            return response()->json(["status"=>"0", "errors"=> ["Request campaign not found or has ended."]], Response::HTTP_NOT_FOUND);
        }
        $this->donationService->createDonation($request->all(), $user, $campaign);

        return response()->json(["status"=>"1", "message"=> "Donation added."], Response::HTTP_CREATED);
    }
}