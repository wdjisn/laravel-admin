<?php

namespace App\Http\Controllers\Api;

use App\Extend\Apay;
use App\Extend\Predis;
use App\Extend\QnPili;
use App\Extend\Upload;
use App\Service\SmsService;
use Illuminate\Http\Request;
use App\Service\SeckillService;

class IndexController
{

    /**
     * 发送短信验证码
     * @param Request $request
     * @throws \AlibabaCloud\Client\Exception\ClientException
     */
    public function sendCode(Request $request)
    {
        $ip = $request->ip();
        $mobile = $request->input('mobile');

        $result = SmsService::sendCode($mobile, $ip);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }
        successReturn($result['data']);
    }

    /**
     * base字符串上传图片
     * @param Request $request
     */
    public function uploadImageBase64(Request $request)
    {
        $file = $request->input('file');

        $result = Upload::base64Image($file);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }
        successReturn($result['data']);
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
    public function userSeckill(Request $request)
    {
        $userId  = time();
        $goodsId = $request->input('goods_id');

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
    public function seckillResult(Request $request)
    {
        $goodsId = $request->input('goods_id');

        $redis  = new Predis();
        $key    = 'SECKILL:SUCCESS:'.$goodsId;
        $result = $redis->hgetall($key);

        successReturn($result);
    }

    /**
     * 创建直播流
     */
    public function createLive()
    {
        $userId = rand(100, 1000);
        $result = QnPili::createStream($userId);

        successReturn($result['data']);
    }

    /**
     * 获取直播列表
     */
    public function getLiveList()
    {
        $list   = Array();
        $result = QnPili::getLiveList();

        if ($result['data']['keys']) {
            foreach ($result['data']['keys'] as $key=>$val) {
                $valArr = explode('-', $val);
                if ($valArr && $valArr[2]) {
                    $tmp = Array();
                    $tmp['live_name'] = $key;
                    $tmp['play_url']  = QnPili::getPlayUrl($valArr[2]);;
                    $list[$key] = $tmp;
                }
            }
        }

        successReturn($list);
    }
}
