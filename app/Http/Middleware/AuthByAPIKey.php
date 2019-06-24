<?php

namespace App\Http\Middleware;

use App\Helpers\APIKeyAuthHelper as Assistance;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\HeaderBag;

class AuthByAPIKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next, $quest = 0)
    {
        // validate api key
        $headers = getallheaders();
        if (!isset($headers['api_key']) or (!$apiKey = $headers['api_key'])) {
            abort(403, 'api_key not found');
        }

        /*
            // get current api key
            $currentAPIKey = Assistance::getCurrentAPIKey();
        */
        // find api key or abort on fail
        if (!$appInfo = Assistance::findAppInfo($apiKey)) {
            abort(403, 'Wrong api key');
        }

        if(!$quest) {
            //  validate token
            if (!$token = $request->header('token', false)) {
                abort(403);
            }
        }

        // set dynamic database connection
        Assistance::setDynamicConnection($appInfo);

        if(!$quest) {
            // get user info
            if (!$user = Assistance::findUserByToken($token, $apiKey)) {
                abort(403, 'Wrong token');
            }
        }

        // set api info and user info
        Assistance::setAppInfo($appInfo);
        if(!$quest) {
            define('GUEST', false);
            Assistance::setUser($user);
        } {
            define('GUEST', true);
        }


        // continue
        return $next($request);
    }



}
