<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/8
 * Time: 16:16
 */

namespace App\Extend;

class Dding
{

    # 此推送方法需要配置ip白名单
    protected static $webhook = 'https://oapi.dingtalk.com/robot/send?access_token=******';

    /**
     * Bug告警
     * @param $param
     */
    public static function bugWarning($param)
    {
        $environment = '测试环境';
        if (env('APP_ENVIRONMENT')) {
            $environment = '正式环境';
        }

        $content = '【' . $environment . '】' . date('Y-m-d H:i:s',$param['created_at']) . "\r\n";
        foreach ($param as $key => $val) {
            if ($key == 'created_at') {
                continue;
            }
            $str = json_encode($val,JSON_UNESCAPED_SLASHES);
            $content = $content . '【' . $key . '】' . $str . "\r\n";
        }

        $dataArr = ['msgtype' => 'text', 'text' => ['content' => $content]];
        $dataStr = json_encode($dataArr);
        self::request_by_curl(self::$webhook, $dataStr);
    }

    /**
     * curl模拟请求
     * @param $remote_server
     * @param $post_string
     * @return bool|string
     */
    protected static function request_by_curl($remote_server, $post_string)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
