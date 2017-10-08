<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermissions
{
    
    public function handle($request, Closure $next)
    {
        $url1 = \Request::segment(1);
        $url2 = \Request::segment(2);
        $currentUrl = "$url1/$url2";

        if(!in_array($currentUrl, session('userMenuShare'))){

            return redirect('page-not-available');
        }

        return $next($request);
        
    }
}
