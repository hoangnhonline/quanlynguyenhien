<?php

namespace App\Http\Middleware;

use Closure, Route, Auth;
use Illuminate\Support\Facades\Log;

class Audit
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
        // dd(Auth::check());
//         if($request->user() && !$request->user()->is_staff){
//             auth()->logout();
//             return redirect('/login');
//         }

        return $next($request);
    }
}
