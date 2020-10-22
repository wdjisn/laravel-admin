<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/29
 * Time: 17:33
 */

namespace App\Http\Middleware;

use Closure;
use App\Extend\Token;

class RequestLog
{

    /**
     * 记录所有请求信息 中间件
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->path() !== 'admin/request/logs') {
            $message = [
                'created_at' => date('Y-m-d H:i:s'),
                'path'       => $request->path(),
                'method'     => $request->method(),
                'ip'         => $request->ip(),
                'param'      => '',
                'header'     => json_encode($request->header(),JSON_UNESCAPED_SLASHES),
            ];
            $param = $request->all();
            if (array_key_exists('password',$param)) {
                $param['password'] = '******';
            }
            $message['param'] = json_encode($param,JSON_UNESCAPED_SLASHES);

            # 保存至log文件
            $message = json_encode($message, JSON_UNESCAPED_SLASHES);
            $path = base_path().'/storage/logs/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $filename = $path . 'request.log';
            $content  = $message . "\r\n";
            file_put_contents($filename, $content, FILE_APPEND);
        }

        return $next($request);
    }
}
