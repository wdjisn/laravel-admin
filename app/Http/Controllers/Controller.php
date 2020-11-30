<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 预览storage文件夹下的图片
     * @param $file_path
     * @param $file_name
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    function imageBrowse($file_path,$file_name)
    {
        return response()->file(storage_path().'/app/upload/image/'.$file_path.'/'.$file_name);
    }

    /**
     * 预览storage文件夹下的视频
     * @param $file_path
     * @param $file_name
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    function videoBrowse($file_path,$file_name)
    {
        return response()->file(storage_path().'/app/upload/video/'.$file_path.'/'.$file_name);
    }
}
