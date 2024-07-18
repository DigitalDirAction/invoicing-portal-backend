<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use F9Web\ApiResponseHelpers;

class AdminMiddleware
{
    use ApiResponseHelpers;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            $reponse = getResponse('', '', "Unauthorized", 403);
            return $this->respondWithSuccess($reponse);
        }
        return $next($request);
    }
}
