<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2022/5/16
 * Time: 10:04
 */

namespace App\Service;

use App\Extend\Oss;
use App\Extend\Qniu;
use Illuminate\Support\Facades\Storage;

class UploadService
{

    private static $imageSize = 2;
    private static $videoSize = 20;
    private static $imagePostfix = ["png", "jpg", "jpeg", "gif"];
    private static $videoPostfix = ["mp4", "avi", "3gp", "wmv", "mov"];

    /**
     * form表单上传文件
     * @param $file
     * @param string $mode
     * @return mixed
     * @throws \Exception
     */
    public static function file($file, $mode = 'storage')
    {
        # 验证后缀
        $postfix = strtolower($file->getClientOriginalExtension());
        if (!$postfix || ($postfix && !in_array($postfix, self::$imagePostfix) && !in_array($postfix, self::$videoPostfix))) {
            return error('不支持此格式文件上传');
        }

        # 验证大小
        $size = $file->getSize() / (1024 * 1024);
        if (in_array($postfix, self::$imagePostfix)) {
            $type = 'image';
            if ($size > self::$imageSize) {
                return error('图片大小不能大于' . self::$imageSize . 'M');
            }
        }
        if (in_array($postfix, self::$videoPostfix)) {
            $type = 'video';
            if ($size > self::$videoSize) {
                return error('视频大小不能大于' . self::$imageSize . 'M');
            }
        }

        # 要存储的文件名
        $name = date('His') . rand(1000, 9999) . '.' . $postfix;

        switch ($mode)
        {
            case 'storage':
                # 存储至storage文件夹
                $date   = date('Ymd');
                $path   = 'upload/' . $type . '/' . $date . '/';
                $result = Storage::putFileAs($path, $file, $name);
                if ($result) {
                    $url  = '/' . $type . '/' .$date . '/' . $name;
                    $data = ['url' => $url, 'full_url' => 'http://' . $_SERVER["HTTP_HOST"] . $url];
                    return success($data);
                }
                return error();
                break;
            case 'qiniu':
                # 存储到七牛云
                $realPath = $file->getRealPath();
                $url = $type . '/' . date('Ymd') . '/' . $name;
                $result = Qniu::uploadFile($realPath,$url);
                if ($result['status']) {
                    $data = ['url' => $result['data']['url'], 'full_url' => config('style.oss.gateway') . $result['data']['url']];
                    return success($data);
                }
                return error($result['msg']);
                break;
            case 'oss':
                # 存储至阿里云OSS
                $realPath = $file->getRealPath();
                $url = $type . '/' . date('Ymd') . '/' . $name;
                $result = Oss::fileUpload($realPath,$url);
                if (!$result['status']) {
                    return error($result['msg']);
                }
                return success($result['data']);
                break;
            default:
                return error();
        }
        return error();
    }

    /**
     * base64字符串上传图片
     * @param $content
     * @param string $mode
     * @return mixed
     */
    public static function base64Image($content, $mode = 'storage')
    {
        # 存储到storage文件夹
        preg_match('/^(data:\s*image\/(\w+);base64,)/',$content,$match);
        if (isset($match[2])) {
            # 验证后缀
            $postfix = strtolower($match[2]);
            if (!in_array($postfix, self::$imagePostfix)) {
                $str = implode(' | ',self::$imagePostfix);
                return error('只能上传 '. $str .' 格式的图片');
            }

            # 验证大小
            $size = strlen(file_get_contents($content)) / (1024 * 1024);
            if ($size > self::$imageSize) {
                return error('图片大小不能大于' . self::$imageSize . 'M');
            }

            switch ($mode)
            {
                case 'storage':
                    # 存储至storage文件夹
                    $name  = date('His') . rand(1000, 9999) . '.' . $postfix;
                    $date  = date('Ymd');
                    $path  = 'image/' . $date . '/' . $name;
                    $image = base64_decode(str_replace($match[1], '', $content));
                    # disk设置参数upload，需要在config/filesystems.php文件中增加upload驱动
                    $disk   = 'upload';
                    $result = Storage::disk($disk)->put($path,$image);
                    if ($result) {
                        $url  = '/image/' . $date . '/' . $name;
                        $data = ['url' => $url, 'full_url' => 'http://' . $_SERVER["HTTP_HOST"] . $url];
                        return success($data);
                    }
                    return error();
                    break;
                case 'qiniu':
                    # 存储到七牛云
                    $name = date('His') . rand(1000, 9999) . '.' . $postfix;
                    $url = 'image/' . date('Ymd') . '/' . $name;
                    $image = base64_decode(str_replace($match[1], '', $content));
                    $result = Qniu::uploadContent($image, $url);
                    if ($result['status']) {
                        $data = ['url' => $result['data']['url'], 'full_url' => config('style.oss.storage') . $result['data']['url']];
                        return success($data);
                    }
                    return error($result['msg']);
                    break;
                case 'oss':
                    # 存储至阿里云OSS
                    $name = date('His') . rand(1000, 9999) . '.' . $postfix;
                    $url = 'image/' . date('Ymd') . '/' . $name;
                    $image = base64_decode(str_replace($match[1], '', $content));
                    $result = Oss::contentUpload($image, $url);
                    if (!$result['status']) {
                        return error($result['msg']);
                    }
                    return success($result['data']);
                    break;
                default:
                    return error();
            }
        } else {
            return error();
        }
    }
}
