<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/16
 * Time: 17:42
 */

namespace App\Http\Controllers\Admin;

use App\Extend\Token;
use App\Extend\Predis;
use Illuminate\Http\Request;
use App\Service\AdminService;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{

    protected $ip;
    protected $userId;
    protected $roleId;
    protected $isAdmin;
    protected $username;
    protected $requestArr;
    protected $requestFile;

    /**
     * 初始化方法
     * BaseController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        # 获取传参数据
        $this->ip = $request->ip();
        $this->requestArr  = $request->input();
        $this->requestFile = $request->file();

        $token = $request->header('token');
        $str   = base64_decode($token);
        $arr   = json_decode($str,true);
        if (!$arr) {
            jError();
        }
        if ((time() - $arr['timestamp']) > 60) {
            jError();
        }
        $sign = md5('appname='.config('style.app.name').'&appsecret='.config('style.app.secret').'&timestamp='.$arr['timestamp']);
        if ($arr['sign'] != $sign) {
            jError();
        }
        $userToken = $arr['token'];
        $result = Token::checkToken($userToken);
        if (!$result) {
            jError();
        }
        $result = (array)$result;
        $userInfo = (array)$result['data'];

        # 从redis获取信息
        $key   = "ADMIN:UID:".$userInfo['id'];
        $redis = new Predis();
        $data  = $redis->get($key);
        if (!$data) {
            jError('登录失效，请重新登录',401);
        }
        $data = json_decode($data,true);

        # 单点登录
        if ($userToken != $data['token']) {
            jError('登录失效，请重新登录',401);
        }

        # 刷新有效期
        $redis->expire($key,3600);

        # 验证用户状态
        $admin = AdminService::getInfoById($userInfo['id']);
        if ($admin['status'] != 1 || $admin['role_status'] != 1) {
            jError('登录失效，请重新登录',401);
        }

        $this->userId   = $admin['id'];
        $this->roleId   = $admin['role_id'];
        $this->isAdmin  = $admin['is_admin'];
        $this->username = $admin['username'];
    }
}
