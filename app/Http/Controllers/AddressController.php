<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Campaign;
use App\Models\User;
use App\Models\Address;
use App\Rules\ArraySameSizeAs;
use Illuminate\Support\Carbon;
use App\Services\Contracts\ICampaignService;
use App\Services\Contracts\IDonationService;
use App\Services\Contracts\IAddressService;



class AddressController extends Controller
{
    protected $addressService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(IAddressService $addressService )
    {
        //
        $this->addressService = $addressService;
    }

    //
    public function addAddresses(Request $request, User $user){
        $validator = Validator::make($request->all(), Address::$rules['create']);
        
        if($validator->fails()){
            $errors = $validator->errors()->toArray();
            $errors["message"] = "Unable to process parameters.";
            return response()->json(["status"=>"0", "errors"=> $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $addressesInputs = $request->input('addresses');
        $addresses = $this->addressService->addUserAddress($addressesInputs, $user);

        return response()->json(['status'=>1, 'message'=>'Addresses created', 'addresses'=>$addresses], Response::HTTP_CREATED);
    }

    public function getAddressByUser(User $user){
        $addresses = $this->addressService->getUserAddresses($user);
        if(is_null($addresses)){
            $errors['message'] = "No addresses found";
            return response()->json(["status"=>0, "errors"=>$errors], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['status'=>1, "message"=>"User's addresses", "addresses"=>$addresses]);
    }
}
