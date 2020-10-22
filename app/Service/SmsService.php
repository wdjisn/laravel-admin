<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/16
 * Time: 16:04
 */

namespace App\Service;

use App\Extend\Predis;
use App\Extend\Acloud;

class SmsService
{

    /**
     * 发送短信验证码
     * @param $mobile
     * @param $ip
     * @return mixed
     * @throws \AlibabaCloud\Client\Exception\ClientException
     */
    public static function sendCode($mobile, $ip)
    {
        # 验证参数
        if (!preg_match('/^1[345789][0-9]{9}$/',$mobile)) {
            return errorMsg('手机号格式不正确');
        }

        # 发送短信风控
        # 同一IP同一手机号，30分钟内发送次数超过10次，此IP拉黑2小时
        $redis = new Predis();
        $ipKey = 'SMS:' . $ip . ':' . $mobile;
        $count = $redis->get($ipKey);
        if ($count > 9) {
            # IP拉黑2小时
            $redis->expire($ipKey,2*60*60);
            return errorMsg('发送过于频繁');
        }

        # 同一手机号，60秒（前端展示的倒计时）内只能发送一次
        $mobileKey = 'SMS:' . $mobile;
        $value = $redis->get($mobileKey);
        if ($value) {
            return errorMsg('发送过于频繁，请稍后重试');
        }

        # 验证码
        $code = rand(100000, 999999);
        $data = ['code' => $code];

        # 发送
        $result = Acloud::sendSms($mobile,'SMS_169897105',$data);
        if (!$result['status']) {
            return errorMsg($result['msg']);
        }
        # 发送成功，记录数据
        $redis->incr($ipKey);
        if (!$count) {
            # 有效期30分钟
            $redis->expire($ipKey,30 *60);
        }
        $redis->set($mobileKey,$code,60);
        return successMsg();
    }

    /**
     * 验证短信验证码
     * @param $mobile
     * @param $code
     * @return mixed
     */
    public static function checkCode($mobile, $code)
    {
        $mobileKey = 'SMS:' . $mobile;
        $redis     = new Predis();
        $value     = $redis->get($mobileKey);
        if (!$value || $value != $code) {
            return errorMsg('验证码错误');
        }
        return successMsg();
    }
}
