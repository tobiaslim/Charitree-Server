<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Campaign;
use App\Models\User;
use App\Rules\ArraySameSizeAs;
use Illuminate\Support\Carbon;
use App\Services\Contracts\ICampaignService;
use App\Services\Contracts\IDonationService;



class CampaignController extends Controller
{
    protected $campaignService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ICampaignService $campaignService )
    {
        //
        $this->campaignService = $campaignService;
    }

    //

    public function createCampaign(Request $request){
        $validator = Validator::make($request->all(),Campaign::$rules['create']);

        if($validator->fails()){
            $errors = $validator->errors()->toArray();
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->campaignService->create($request->all());
        return response()->json(["status"=>"1", "message"=>"Campaign created!"], Response::HTTP_CREATED);

    }

    public function getCampaigns(Request $request){
        $validator = Validator::make($request->all(),Campaign::$rules['get']);

        if($validator->fails()){
            $errors = $validator->errors()->toArray();
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $max = null;
        if($request->has('max')){
            $max = (int) $request->input('max');
        }

        $campaigns = $this->campaignService->getAllCampaigns($max);
        if(is_null($campaigns)){
            $errors['message'] = "No campaigns found";
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_NOT_FOUND);
        }
        return response()->json(["status"=>"1", "messages"=> "All campaigns.", "campaigns"=>$campaigns], Response::HTTP_OK);
    }

    public function getCampaignByCampaignID($id){
        $campaign = $this->campaignService->getCampaignByCampaignID($id);
        if(is_null($campaign)){
            $errors['message'] = "Campaign not found.";
            return response()->json(['status'=>0, 'errors'=>$errors], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['status'=>1, 'message'=>'Campaign found.', 'campaign'=>$campaign]);
    }

    public function getAllCampaignBySession(Request $request,User $user){
        $validator = Validator::make($request->all(),Campaign::$rules['get']);

        if($validator->fails()){
            $errors = $validator->errors()->toArray();
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $campaigns = $this->campaignService->getAllCampaignBySession($user);

        if(is_null($campaigns)){
            $errors['message'] = "No campaigns found";
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_NOT_FOUND);
        }
        return response()->json(["status"=>"1", "messages"=> "All campaigns.", "campaigns"=>$campaigns], Response::HTTP_OK);
    }


}
