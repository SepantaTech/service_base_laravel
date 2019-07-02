<?php
/**
 * Created by PhpStorm.
 * User: amin
 * Date: 6/18/19
 * Time: 2:36 PM
 */

namespace App\Helpers;


use App\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class APIKeyAuthHelper
{
    /**
     * Find app info by it's api key and return array of info
     * @param $apiKey
     * @return
     */
    public static function findAppInfo($apiKey)
    {
        // from cache

        // from db
        $select = DB::select("Select * from apps where api_key=?", [$apiKey]);
        if (is_array($select) and count($select)) {
            return (array)$select[0];
        }
        // get from Auth service and save to db


        return false;
    }

    /**
     * Find user by token and return user Object
     * @param $token
     * @return User
     */
    public static function findUserByToken($token, $apiKey)
    {
        // get user id from cache
        $userId = Cache::get("userId" . $token);

        // get user using user id
        if ($userId) {
            if ($fromDB = self::getUserById($userId)) {
                return $fromDB;
            }
        }

        // get user from Users service
        if ($fromService = UsersServiceHelper::getUserInfo($token, env("API_KEY"))) {

            $userId = $fromService->id;
            //  cache user id for next 24 hours
            Cache::put('userId' . $token, $userId, now()->addDay());

            // try again to find user info locally
            if ($fromDB = self::getUserById($userId)) {
                return $fromDB;
            }
            // save user if local info not found
            $fromService->save();

            // cache user
            Cache::put('user' . $userId, $fromService, now()->addDay());

            return $fromService;

        }

        return null;
    }

    private static function getUserById($id)
    {
        // from cache
        if ($value = Cache::get('user' . $id)) {
            return $value;
        }
        // from db
        return User::query()->find($id);
    }

    /**
     * Keep user id
     * @param User $user
     */
    public static function setUser(User $user)
    {
        define('USER_ID', $user->id);
        define('USER_TOKEN', $user->token);
    }

    /**
     * Keep app info
     * @param $appInfo
     */
    public static function setAppInfo($appInfo)
    {
        define('APP_ID', $appInfo['id']);
        define('API_KEY', $appInfo['api_key']);
    }

    /**
     * set dynamic database connection config
     * @param $appInfo
     */
    public static function setDynamicConnection($appInfo)
    {
        $dbName = $appInfo['id'] . '_' . env('App_NAME_POSTFIX');
        $mysqlConnectionConfig = config('database.connections.mysql');
        $mysqlConnectionConfig['database'] = $dbName;
        Config::set("database.connections.dynamic", $mysqlConnectionConfig);

    }
}