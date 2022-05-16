<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/16
 * Time: 17:43
 */

namespace App\Http\Controllers\Admin;

use App\Jobs\Snotify;
use App\Extend\Predis;
use App\Rules\AdminRule;
use App\Model\SmsNotify;
use App\Extend\Phpoffice;
use App\Service\RoleService;
use App\Extend\ServerMonitor;
use App\Service\AdminService;
use App\Service\UploadService;

class IndexController extends BaseController
{

    /**
     * 获取服务器参数
     */
    public function getServerMonitor()
    {
        $systemIns = new ServerMonitor();

        $data['runTime'] = $systemIns->getUpTime();            # 获取运行时间
        $data['memory']  = $systemIns->getMem(true);   # 获取内存信息
        $data['cpu']     = $systemIns->getCPU();               # 获取CPU使用率

        jSuccess($data);
    }

    /**
     * 退出
     */
    public function loginOut()
    {
        $key   = "ADMIN:UID:".$this->userId;
        $redis = new Predis();
        $redis->del($key);
        jSuccess();
    }

    /**
     * 修改密码
     */
    public function changePassword()
    {
        $data['password'] = $this->requestArr['password'];
        $data['old_password'] = $this->requestArr['old_password'];
        $data['confirm_password'] = $this->requestArr['confirm_password'];

        # 验证参数
        $adminRule = new AdminRule();
        if (!$adminRule->scene('change_password')->check($data)) {
            jError($adminRule->getError());
        }

        $id = $this->userId;
        $result = AdminService::changePassword($id,$data['old_password'],$data['password']);
        if (!$result['status']) {
            jError($result['msg']);
        }

        jSuccess();
    }

    /**
     * 获取权限
     */
    public function getPermission()
    {
        $permission = RoleService::getPermission($this->roleId,$this->isAdmin);

        jSuccess($permission);
    }

    /**
     * 上传文件
     */
    public function uploadFile()
    {
        $file = $this->requestFile['file'];

        $result = UploadService::file($file);
        if (!$result['status']) {
            jError($result['msg']);
        }
        jSuccess($result['data']);
    }

    /**
     * 批量发送短信通知(使用队列)
     */
    public function batchSendNotify()
    {
        $excel  = $this->requestFile['excel'];
        $result = Phpoffice::import($excel);
        if (!$result['status']) {
            jError($result['msg']);
        }

        $data = Array();
        foreach ($result['data'] as $key=>$val) {
            if ($val[0]) {
                $info = Array();
                $info['value']      = $val[0];
                $info['status']     = 0;
                $info['updated_at'] = time();
                $info['created_at'] = time();
                $data[] = $info;
            }
        }
        SmsNotify::insert($data);

        SmsNotify::where('status',0)->chunk(1000, function ($notifies) {
            # 待发送短信通知入队
            foreach ($notifies as $notify) {
                # dispatch 分发任务
                # delay    延迟2秒分发
                Snotify::dispatch($notify)->delay(now()->addSeconds(2));
            }
        });
        # php artisan queue:work   消费队列
        # 正式环境下，需要使用 Supervisor 进程守护

        jSuccess();
    }
}
