<?php

namespace App\Exceptions;

use App\Extend\Dding;
use Exception;
use App\Model\ErrorLog;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        # 收集错误信息
        if ($exception->getMessage()) {
            $message = [
                'created_at' => time(),
                'message'    => $exception->getMessage(),
                'file'       => $exception->getFile(),
                'line'       => $exception->getLine(),
                'ip'         => $request->ip(),
                'url'        => $request->url(),
                'method'     => $request->method(),
                'param'      => '',
                'header'     => json_encode($request->header(),JSON_UNESCAPED_SLASHES)
            ];
            $param = $request->all();
            if (array_key_exists('password',$param)) {
                $param['password'] = '******';
            }
            $message['param'] = json_encode($param,JSON_UNESCAPED_SLASHES);

            # 钉钉推送
            Dding::bugWarning($message);

            # 保存至mysql
            ErrorLog::addInfo($message);
        }

        return parent::render($request, $exception);
    }
}
