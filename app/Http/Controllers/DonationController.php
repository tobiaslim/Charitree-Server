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
    protected $DonationService;
     /**
     * Create a new controller instance.
     *
     * @return void
     */ 
    public function __construct(IDonationService $DonationService)
    {
        $this->DonationService = $DonationService;
    }

    public function getAllDonations(Request $request, User $user){
       if(!is_null($user->donation()))
       {
           return $this->DonationService->getAllDonations($user);
       }
       
       else
       {
        return response()->json(['status' => '0', 'errors' => ["message"=>'There is no donation or something went wrong']]);
       }
    }
}