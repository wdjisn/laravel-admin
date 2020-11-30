<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/9
 * Time: 16:48
 */

namespace App\Extend;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Acloud
{

    /**
     * 获取配置文件
     * @return array
     */
    private static function getConfig()
    {
        # 使用RAM账号,需要授权
        $key    = config('style.sms.key');
        $secret = config('style.sms.secret');
        $sign   = config('style.sms.sign');

        return compact('key','secret','sign');
    }

    /**
     * 发送短信
     * @param $mobile
     * @param $template
     * @param $param
     * @return mixed
     * @throws ClientException
     */
    public static function sendSms($mobile, $template, $param)
    {
        $config = self::getConfig();

        AlibabaCloud::accessKeyClient($config['key'],$config['secret'])
            ->regionId('cn-hangzhou')                           # 指定请求的地域
            ->asDefaultClient();

        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')                           # 指定产品
                // ->scheme('https')                            # https | http
                ->version('2017-05-25')                         # 指定产品版本
                ->action('SendSms')                             # 指定产品接口
                ->method('POST')                                # 指定请求方式
                ->host('dysmsapi.aliyuncs.com')                 # 指定域名则不会寻址，如认证方式为Bearer Token的服务则需要指定。
                ->options([
                    'query' => [
                        'PhoneNumbers'  => $mobile,             # 手机号
                        'SignName'      => $config['sign'],     # 签名
                        'TemplateCode'  => $template,           # 模板
                        'TemplateParam' => json_encode($param)  # 模板参数
                    ],
                ])
                ->request();
            $res = $result->toArray();
            if ($res['Code'] != "OK") {
                return errorMsg($result['Message']);
            }
            return successMsg();
        } catch (ClientException $e) {
            return errorMsg($e->getErrorMessage());
        } catch (ServerException $e) {
            return errorMsg($e->getErrorMessage());
        } catch (\Exception $e) {
            return errorMsg($e->getMessage());
        }
    }
}
