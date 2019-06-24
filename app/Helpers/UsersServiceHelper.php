<?php
/**
 * Created by PhpStorm.
 * User: amin
 * Date: 6/22/19
 * Time: 1:20 PM
 */

namespace App\Helpers;


use App\User;

class UsersServiceHelper
{
    public static function getUserInfo($token, $apiKey)
    {
        $url = config('gamehub.urls.user.profile.get');

        $userInfoRequestString = self::request($url,
            [],
            ["token: " . $token, "api_key: " . $apiKey]);

        $userInfoRequest = json_decode($userInfoRequestString['body'], true);
        if (!is_array($userInfoRequest) || !isset($userInfoRequest['isSuccess']) || $userInfoRequest['isSuccess'] != true || !is_array($userInfoRequest['result']))
            return false;

        $result = $userInfoRequest['result'];
        $data = [
            'id' => isset($result['id']) ? $result['id'] : "",
            'username' => isset($result['username']) ? $result['username'] : "",
            'name' => isset($result['name']) ? $result['name'] : "",
            'avatar' => isset($result['avatar']) ? $result['avatar'] : "",
            'profile' => isset($result['profile']) ? $result['profile'] : [],
        ];
        $user = new User;
        $user->id = $data['id'];
        $user->username = $data['username'];
        $user->name = $data['name'];
        $user->avatar = $data['avatar'];
        $user->profile = $data['profile'];

        return $user;

    }


    private static function request($url, $data = [], $headers = [], $auth = [])
    {

        if (is_string($data)) {
            $postData = $data;
        } else {
            $postData = '';
            //create name value pairs separated by &
            foreach ($data as $k => $v) {
                if (is_array($v))
                    foreach ($v as $_v)
                        $postData .= $k . '[]=' . urlencode($_v) . '&';
                else
                    $postData .= $k . '=' . urlencode($v) . '&';

            }
            rtrim($postData, '&');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, mb_strlen($postData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        if (isset($auth["username"]) && isset($auth["password"]))
            curl_setopt($ch, CURLOPT_USERPWD, $auth['username'] . ":" . $auth['password']);
        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        $result['headers'] = substr($response, 0, $header_size);
        $result['headers'] = explode("\r\n", trim($result['headers']));
        $result['body'] = substr($response, $header_size);

        return $result;
    }
}