<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Contracts\Repository\ICampaignRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Campaign;



class CampaignController extends Controller
{
    protected $campaignRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ICampaignRepository $campaignRepository )
    {
        //
        $this->campaignRepository = $campaignRepository;
    }

    //

    public function createCampaign(Request $request){
        $validator = Validator::make($request->all(),Campaign::$rules['create']);

        if($validator->fails()){
            $errors = $validator->errors();
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->campaignRepository->create($request->all());
        return response()->json(["status"=>"1", "message"=>"Campaign created!"], Response::HTTP_CREATED);

    }
}
