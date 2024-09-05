<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BlockMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()){
            $user = Auth::user();
            if($user->deleted_at !== null){
                return \response()->json([
                    "status"=> "blocked",
                    "message"=> "User blocked",
"reason"=> "You have been blocked by an administrator"
                ],403);
            }
        }

        return $next($request);
    }
}
