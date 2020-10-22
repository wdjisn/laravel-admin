<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/9
 * Time: 10:16
 */

namespace App\Extend;

use OSS\OssClient;
use OSS\Core\OssException;

class Oss
{

    /**
     * 获取配置文件
     * @return array
     */
    private static function getConfig()
    {
        # 使用RAM账号,需要授权
        $bucket   = env('OSS_BUCKET');
        $key      = env('OSS_KEY');
        $secret   = env('OSS_SECRET');
        $endpoint = env('OSS_ENDPOINT');
        $gateway  = env('OSS_GATEWAY');

        return compact('bucket','key','secret','endpoint','gateway');
    }

    /**
     * 文件上传
     * @param $file
     * @param $path
     * @return mixed
     */
    public static function fileUpload($file, $path)
    {
        try{
            $config = self::getConfig();
            $ossClient = new OssClient($config['key'], $config['secret'], $config['endpoint']);
            $ossClient->uploadFile($config['bucket'], $path, $file);
            return successMsg(['url' => $config['gateway'].$path]);
        } catch (OssException $e) {
            return errorMsg($e->getMessage());
        }
    }

    /**
     * 字符串上传
     * @param $content
     * @param $path
     * @return mixed
     */
    public static function contentUpload($content, $path)
    {
        try{
            $config = self::getConfig();
            $ossClient = new OssClient($config['key'], $config['secret'], $config['endpoint']);
            $ossClient->putObject($config['bucket'], $path, $content);
            return successMsg(['url' => $config['gateway'].$path]);
        } catch(OssException $e) {
            return errorMsg($e->getMessage());
        }
    }
}
