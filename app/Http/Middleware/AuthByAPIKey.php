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
    public function handle(\Illuminate\Http\Request $request, Closure $next)
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
            abort(403, 'Wrong api key or token');
        }

        //  validate token
        if (!$token = $request->header('token', false)) {
            abort(403);
        }

        // get user info
        if (!$user = Assistance::findUserByToken($token)) {
            abort(403, 'Wrong api key or token');
        }

        // set api info and user info
        Assistance::setAppInfo($appInfo);
        Assistance::setUser($user);

        // set dynamic database connection (Note: App dynamic database and tables must create after app registration)
        $dbName = $appInfo['id'] . '_' . env('App_NAME_POSTFIX');
        $mysqlConnectionConfig = config('database.connections.mysql');
        $mysqlConnectionConfig['database'] = $dbName;
        Config::set("database.connections.dynamic", $mysqlConnectionConfig);

        // continue
        return $next($request);
    }
}
