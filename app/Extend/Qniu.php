<?php

namespace App\Extend;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class Qniu
{

    /**
     * 获取token
     * @return string
     */
    public static function getToken()
    {
        $redis = new Predis();
        $token = $redis->get('QINIU:TOKEN');
        if (!$token) {
            # 获取配置文件
            $key    = config('style.qiniu.key');
            $secret = config('style.qiniu.secret');
            $bucket = config('style.qiniu.bucket');

            # 构建鉴权对象
            $auth = new Auth($key, $secret);

            # 生成上传 Token
            $token = $auth->uploadToken($bucket);

            # 存储信息至Redis
            $redis->set('QINIU:TOKEN',$token,3500);
        }

        return $token;
    }

    /**
     * 文件上传
     * @param $file
     * @param $path
     * @return mixed
     * @throws \Exception
     */
    public static function uploadFile($file, $path)
    {
        # 获取token
        $token = self::getToken();

        # 初始化 uploadManager对象
        $uploadMgr = new UploadManager();

        # 调用 uploadManager的 putFile方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $path, $file);

        if ($err !== null) {
            return error('上传失败，请稍后重试');
        } else {
            return success(['url' => '/'. $ret['key']]);
        }
    }

    /**
     * 字符串上传
     * @param $content
     * @param $path
     * @return mixed
     */
    public static function uploadContent($content, $path)
    {
        # 获取token
        $token = self::getToken();

        # 初始化 uploadManager对象
        $uploadMgr = new UploadManager();

        # 调用 uploadManager的 putFile方法进行文件的上传
        list($ret, $err) = $uploadMgr->put($token, $path, $content);

        if ($err !== null) {
            return error('上传失败，请稍后重试');
        } else {
            return success(['url' => '/'. $ret['key']]);
        }
    }
}
