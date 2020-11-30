<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/18
 * Time: 17:59
 */

namespace App\Extend;

use Firebase\JWT\JWT;

class Token
{

    /**
     * 生成token
     * @param array $param
     * @return string
     */
    public static function getToken($param = Array())
    {
        # 生成token
        $data = [
            // 'iss' => "http://example.org",      // 签发者 可选
            // 'aud' => "http://example.com",      // 接收该JWT的一方，可选
            // "iat"  => $time,                    // 签发时间
            // "nbf"  => $time,                    // (Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            // 'exp'  => $time ,                   // 过期时间
            'data' => $param                       // 自定义信息，不要定义敏感信息
        ];
        $secret = config('style.app.jwt_secret');
        $token  = JWT::encode($data,$secret);

        return $token;
    }

    /**
     * 校验token
     * @param $token
     * @return bool|object
     */
    public static function checkToken($token)
    {
        try {
            $secret = config('style.app.jwt_secret');
            $data   = JWT::decode($token, $secret, array('HS256'));

            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }
}
