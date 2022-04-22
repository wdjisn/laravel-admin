<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/16
 * Time: 17:43
 */

namespace App\Http\Controllers\Admin;

use App\Extend\Qniu;
use App\Extend\Token;
use App\Extend\Predis;
use App\Rules\AdminRule;
use App\Service\LogService;
use Illuminate\Http\Request;
use App\Service\AdminService;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{

    /**
     * 登录
     * @param Request $request
     */
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        # 验证参数
        $adminRule = new AdminRule();
        if (!$adminRule->scene('login')->check(['username' => $username,'password' => $password])) {
            jError($adminRule->getError());
        }

        # 验证是否可以登录
        $result = AdminService::loginCheck($username,$password);
        if (!$result['status']) {
            jError($result['msg']);
        }
        $info = $result['data'];

        # 更新登录信息
        $info['ip'] = $request->getClientIp();
        AdminService::saveLoginInfo($info);

        # 生成token
        $token = Token::getToken(['id' => $info['id'],'username' => $info['username'],'time' => time()]);

        # 存储信息至Redis
        $val   = ['token' => $token];
        $key   = "ADMIN:UID:".$info['id'];
        $redis = new Predis();
        $redis->set($key,$val,3600);

        $data['token'] = $token;
        $data['timestamp'] = time();
        $data['appname'] = config('style.app.name');

        # 统计未处理bug数量
        $errorCount = LogService::getErrorCount(['status' => 0]);

        jSuccess(['username' => $info['username'],'token' => $token,'error_count' => $errorCount]);
    }

    /**
     * 获取七牛云token
     * vue直接上传文件至七牛云使用
     */
    public function getQiniuToken()
    {
        $token = Qniu::getToken();

        $data = ['uptoken' => $token];
        return json_encode($data);
    }
}
