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
use App\Extend\Upload;
use App\Model\SmsNotify;
use App\Extend\Phpoffice;
use App\Rules\AdminRule;
use App\Service\AdminService;
use App\Service\SmsService;
use App\Service\RoleService;
use App\Service\SeckillService;

class IndexController extends BaseController
{

    /**
     * 退出
     */
    public function loginOut()
    {
        $key   = "ADMIN:UID:".$this->userId;
        $redis = new Predis();
        $redis->del($key);
        successReturn();
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
            errorReturn($adminRule->getError());
        }

        $id = $this->userId;
        $result = AdminService::changePassword($id,$data['old_password'],$data['password']);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }

        successReturn();
    }

    /**
     * 获取权限
     */
    public function getPermission()
    {
        $permission = RoleService::getPermission($this->roleId,$this->isAdmin);

        successReturn($permission);
    }

    /**
     * 上传图片
     */
    public function uploadFileImage()
    {
        $file = $this->requestFile['file'];

        $result = Upload::fileImage($file);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }
        successReturn($result['data']);
    }

    /**
     * base字符串上传图片
     */
    public function uploadBase64Image()
    {
        $file = $this->requestArr['file'];

        $result = Upload::base64Image($file);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }
        successReturn($result['data']);
    }

    /**
     * 发送短信验证码
     * @throws \AlibabaCloud\Client\Exception\ClientException
     */
    public function sendCode()
    {
        $ip = $this->ip;
        $mobile = $this->requestArr['mobile'];

        $result = SmsService::sendCode($mobile,$ip);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }
        successReturn($result['data']);
    }

    /**
     * 批量发送短信通知(使用队列)
     */
    public function batchSendNotify()
    {
        $excel  = $this->requestFile['excel'];
        $result = Phpoffice::import($excel);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }

        $data = Array();
        foreach ($result['data'] as $key=>$val) {
            if ($key) {
                $info = Array();
                $info['value']      = $val[0];
                $info['status']     = 0;
                $info['updated_at'] = time();
                $info['created_at'] = time();
                $data[] = $info;
            }
        }
        SmsNotify::insert($data);

        SmsNotify::chunk(1000, function ($notifies) {
            # 待发送短信通知入队
            foreach ($notifies as $notify) {
                $job = new Snotify($notify);
                $job->delay(5);
                $this->dispatch($job);
            }
        });

        # php artisan queue:work   消费队列

        successReturn();
    }

    /**
     * 创建秒杀
     */
    public function createSeckill()
    {
        $result = SeckillService::storage(1);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }
        successReturn($result['data']);
    }

    /**
     * 用户秒杀
     */
    public function userSeckill()
    {
         $userId  = $this->userId;
         $goodsId = $this->requestArr['goods_id'];

        # 访问用户入队
        $userList = SeckillService::userList($userId);
        if (!$userList) {
            errorReturn('人数过多，请稍后重试');
        }

        # 验证是否已经抢购成功
        $redis = new Predis();
        $successKey = 'SECKILL:SUCCESS:'.$goodsId;
        $successVal = $redis->hgetall($successKey);
        if (in_array($userId, $successVal)) {
            errorReturn('你已经秒杀过了');
        }

        # 从队列中取出商品
        $goodsKey = 'SECKILL:GOODS:'.$goodsId;
        $goodsVal = $redis->lpop($goodsKey);
        if (!$goodsVal) {
            errorReturn('很遗憾，商品抢光了');
        }
        # 存储至hash值
        $redis->hset($successKey, $goodsVal, $userId);

        # 将用户从队列里面弹出,允许下一个用户进来
        $redis->rpop('SECKILL:USER');

        successReturn('恭喜你，秒杀成功');
    }

    /**
     * 显示秒杀结果
     */
    public function seckillResult()
    {
        $goodsId = $this->requestArr['goods_id'];

        $redis  = new Predis();
        $key    = 'SECKILL:SUCCESS:'.$goodsId;
        $result = $redis->hgetall($key);

        v($result);
    }
}
