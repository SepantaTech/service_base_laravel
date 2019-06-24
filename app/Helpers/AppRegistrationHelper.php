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
use Illuminate\Support\Facades\Schema;

class AppRegistrationHelper
{
    public static function getAppInfo($id){
        return RegisteredApp::find($id);
    }

    public static function saveApp(RegisteredApp $registeredApp){
        $registeredApp->save();
        return $registeredApp;
    }

    public static function createDynamicDatabases(RegisteredApp $registeredApp){
        // create database
        $dbName = $registeredApp->id . '_' . env('App_NAME_POSTFIX');
        if(!DB::statement("CREATE DATABASE $dbName")){
            throw new \Exception('Failed on database creation');
        }

        APIKeyAuthHelper::setDynamicConnection([
            'id' => $registeredApp->id,
            'api_key' => $registeredApp->api_key,
        ]);

        // create users table
        Schema::connection('dynamic')->create('users', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->nullable();
            $table->string('name')->nullable();
            $table->string('avatar')->nullable();
            $table->longText('profile')->nullable();

            $table->timestamps();
        });

    }
}