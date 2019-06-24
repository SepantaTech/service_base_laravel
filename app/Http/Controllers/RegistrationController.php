<?php

namespace App\Http\Controllers;

use App\Helpers\AppRegistrationHelper;
use App\Http\Middleware\AuthByAPIKey;
use App\Http\Middleware\OnlyGamehubCDN;
use App\RegisteredApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware(AuthByAPIKey::class . ':1');
        $this->middleware(OnlyGamehubCDN::class);
    }

    public function registerApp(Request $request)
    {
        //validate
        try {
            $validInput = request()->validate([
                'app_id' => ['required', 'string'],
                'salt' => ['required', 'string'],
                'allowed_ips' => ['required'],
            ]);

        } catch (ValidationException $exception) {
            return response([
                'ok' => false,
                'result' => null,
                'error' => true,
                'description' => 'The given data was invalid.',
            ]);

        }
        // get allowed ips
        $allowedIps = ((is_array($validInput['allowed_ips'])) ? $validInput['allowed_ips'] : [$validInput['allowed_ips']]);
        // validate ips
        if (!count($allowedIps)) abort(400);

        foreach ($allowedIps as $ip)
            if (!is_string($ip))
                abort(400);

        //check if app already registered
        $appInfo = AppRegistrationHelper::getAppInfo($validInput['app_id']);


        if ($appInfo) return response([
            'ok' => false,
            'result' => null,
            'error' => true,
            'description' => 'App already registred',
        ], 400);

        // register
        try {

            $registeredApp = new RegisteredApp;
            $registeredApp->salt = $validInput['salt'];
            $registeredApp->id = $validInput['app_id'];
            $registeredApp->allowed_ips = $validInput['allowed_ips'];
            $registeredApp->api_key = Hash::make(json_encode($registeredApp));
            $registeredApp = AppRegistrationHelper::saveApp($registeredApp);

            // todo create dynamic database

            $createDBResult = AppRegistrationHelper::createDynamicDatabases($registeredApp);

        } catch (\Exception $exception) {
            // remove registered app
            $registeredApp->delete();

            return response([
                'result' => null,
                'ok' => false,
                'error' => true,
                'description' => 'Failed on save',
            ]);
        }


        return response([
            'result' => ['api_key' => $registeredApp->api_key],
            'ok' => true,
        ]);

    }

}
