<?php

namespace App\Extend;

class Wx
{

    /**
     * 获取openid
     * @param $code
     * @return mixed
     */
    public static function getOpenId($code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . config('style.weixin.id') . "&secret=" . config('style.weixin.secret') . "&code=" . $code . "&grant_type=authorization_code ";

        # 获取用户的openid
        $res = self::http_curl($url);
        $data = json_decode($res, true);

        return $data['openid'];
    }

    /**
     * 通过code 获取用户信息
     * @param $code
     * @return false|mixed
     */
    public static function getUserByOpenId($code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . config('style.weixin.id') . "&secret=" . config('style.weixin.secret') . "&code=" . $code . "&grant_type=authorization_code ";

        # 获取用户的openid
        $res  = self::http_curl($url);
        $data = json_decode($res, true);
        if (!empty($data['access_token']) && !empty($data['openid'])) {
            $url      = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $data['access_token'] . "&openid=" . $data['openid'] . "&lang=zh_CN";
            $userInfo = self::http_curl($url);
            $userInfo = json_decode($userInfo, true);
            return $userInfo;
        }
        return false;
    }

    /**
     * url请求
     * @param $url
     * @return bool|string
     */
    private static function http_curl($url)
    {
        $curl = curl_init();
        // 设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        // 设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // 执行命令
        $data = curl_exec($curl);
        // 关闭URL请求
        curl_close($curl);
        // 显示获得的数据
        return $data;
    }
}
