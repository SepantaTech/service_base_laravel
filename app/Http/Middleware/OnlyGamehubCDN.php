<?php

namespace App\Http\Middleware;

use Closure;

class OnlyGamehubCDN
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
        if(!defined('APP_ID') or APP_ID != 'gamehub'){
            abort(403);
        }
        return $next($request);
    }
}
