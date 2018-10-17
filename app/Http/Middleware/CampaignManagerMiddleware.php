<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class CampaignManagerMiddleware
{
    protected $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Check all incoming request that requires at least a campaign manager access level
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $campaignManager = $this->user->campaignManager;
        if(is_null($campaignManager)){
            return response()->json(['status' => '0', 'message' => 'Only allowed for campaign manager'], Response::HTTP_FORBIDDEN); 
        }
        return $next($request);
    }
}
