<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/8
 * Time: 17:46
 */

namespace App\Extend;

use Illuminate\Support\Facades\Storage;

class Upload
{

    protected static $imageSize = 2;
    protected static $imagePostfix = ["png", "jpg", "jpeg", "gif"];

    /**
     * form表单上传图片
     * @param $file
     * @return mixed
     */
    public static function fileImage($file)
    {
        # 验证后缀
        $postfix = strtolower($file->getClientOriginalExtension());
        if ($postfix && !in_array($postfix, self::$imagePostfix)) {
            $str = implode(' | ',self::$imagePostfix);
            return errorMsg('只能上传 '. $str .' 格式的图片');
        }

        # 验证大小
        $size = $file->getSize() / (1024 * 1024);
        if ($size > self::$imageSize) {
            return errorMsg('图片大小不能大于' . self::$imageSize . 'M');
        }

        # 要存储的文件名
        $name = date('His') . rand(1000, 9999) . '.' . $postfix;

        # 存储至阿里云OSS
        $realPath = $file->getRealPath();
        $url = 'images/' . date('Ymd') . '/' . $name;
        $result = Oss::fileUpload($realPath,$url);
        if (!$result['status']) {
            return errorMsg($result['msg']);
        }
        return successMsg($result['data']);

        /*# 存储至storage文件夹
        $path   = 'upload/images/' . date('Ymd') . '/';
        $result = Storage::putFileAs($path, $file, $name);
        if ($result) {
            return successMsg(['url' => $path . $name]);
        }
        return errorMsg();*/
    }

    /**
     * base64字符串上传图片
     * @param $content
     * @return mixed
     */
    public static function base64Image($content)
    {
        # 存储到storage文件夹
        preg_match('/^(data:\s*image\/(\w+);base64,)/',$content,$match);
        if (isset($match[2])) {
            # 验证后缀
            $postfix = strtolower($match[2]);
            if ($postfix && !in_array($postfix, self::$imagePostfix)) {
                $str = implode(' | ',self::$imagePostfix);
                return errorMsg('只能上传 '. $str .' 格式的图片');
            }

            # 验证大小
            $size = strlen(file_get_contents($content)) / (1024 * 1024);
            if ($size > self::$imageSize) {
                return errorMsg('图片大小不能大于' . self::$imageSize . 'M');
            }

            # 存储至阿里云OSS
            $name   = date('His') . rand(1000, 9999) . '.' . $postfix;
            $url    = 'images/' . date('Ymd') . '/' . $name;
            $image  = base64_decode(str_replace($match[1], '', $content));
            $result = Oss::contentUpload($image,$url);
            if (!$result['status']) {
                return errorMsg($result['msg']);
            }
            return successMsg($result['data']);

            /*# 存储至storage文件夹
            $name  = date('His') . rand(1000, 9999) . '.' . $postfix;
            $path  = 'images/' . date('Ymd') . '/' . $name;
            $image = base64_decode(str_replace($match[1], '', $content));
            # disk设置参数upload，需要在config/filesystems.php文件中增加upload驱动
            $disk   = 'upload';
            $result = Storage::disk($disk)->put($path,$image);
            if ($result) {
                return successMsg(['url' => $disk . '/' . $path]);
            }
            return errorMsg();*/
        } else {
            return errorMsg();
        }
    }
}
