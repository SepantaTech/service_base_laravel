<?php
/**
 * Created by PhpStorm.
 * User: amin
 * Date: 6/19/19
 * Time: 6:15 PM
 */

namespace App\Helpers;

use App\RegisteredApp;
use Illuminate\Support\Facades\DB;

class AppRegistrationHelper
{
    public static function getAppInfo($id){
        return RegisteredApp::find($id);
    }

    public static function saveApp(RegisteredApp $registeredApp){
        $registeredApp->save();
        return $registeredApp;
    }

}