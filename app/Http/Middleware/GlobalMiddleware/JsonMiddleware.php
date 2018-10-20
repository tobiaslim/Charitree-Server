<?php

namespace App\Http\Middleware\GlobalMiddleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class JsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->isJson()){
            return $next($request);
        }
        return response()->json(["message"=>"Invalid request format"], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }
}
