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

    public function createDonation(Request $request, User $user, IDonationService $donationService, $id){
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

        $campaign = $this->campaignService->find($id);
        $today = new Carbon;
        if($campaign == null || $campaign->end_date->lt(new Carbon)){
            return response()->json(["status"=>"0", "errors"=> ["Request campaign not found or has ended."]], Response::HTTP_NOT_FOUND);
        }
        $donationService->createDonation($request->all(), $user, $campaign);

        return response()->json(["status"=>"1", "message"=> "Donation added."], Response::HTTP_CREATED);
    }
}
