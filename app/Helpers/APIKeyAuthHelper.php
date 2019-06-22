<?php
/**
 * Created by PhpStorm.
 * User: amin
 * Date: 6/18/19
 * Time: 2:36 PM
 */

namespace App\Helpers;


use App\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class APIKeyAuthHelper
{
    /**
     * Find app info by it's api key and return array of info
     * @param $apiKey
     * @return bool
     */
    public static function findAppInfo($apiKey) {
        // from cache

        // from db
        $select = DB::select("Select * from apps where api_key=?", [$apiKey]);
        if(is_array($select) and count($select)){
            return (array)$select[0];
        }
        // from Auth service

        return false;
    }

    /**
     * Find user by token and return user Object
     * @param $token
     * @return User
     */
    public static function findUserByToken($token) {
        // get from cache

        // get from db
        return User::query()->where('token', $token)->first();
        // get from Users service

        return null;
    }

    /**
     * Keep user id
     * @param User $user
     */
    public static function setUser(User $user){
        define('USER_ID' , $user->id);
        define('USER_TOKEN', $user->token);
    }

    /**
     * Keep app info
     * @param $appInfo
     */
    public static function setAppInfo($appInfo){
        define('APP_ID', $appInfo['id']);
    }

    /**
     * set dynamic database connection config
     * @param $appInfo
     */
    public static function setDynamicConnection($appInfo){
        $dbName = $appInfo['id'] . '_' . env('App_NAME_POSTFIX');
        $mysqlConnectionConfig = config('database.connections.mysql');
        $mysqlConnectionConfig['database'] = $dbName;
        Config::set("database.connections.dynamic", $mysqlConnectionConfig);

    }
 }