<?php

namespace App\Http\Middleware;

use Closure;
use Request;
use App\User;
class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        if(User::where('token', $request->header('token'))->first()){
            return $next($request);
        }else{
            return abort(403);   
        }
    }
}
