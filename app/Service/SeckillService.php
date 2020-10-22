<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/30
 * Time: 18:01
 */

namespace App\Service;

use App\Model\Goods;
use App\Extend\Predis;

class SeckillService
{

    private static $limit = 300;

    /**
     * 将商品存入redis队列中
     * @param $goods_id
     * @return mixed
     */
    public static function storage($goods_id)
    {
        $info = Goods::getInfo(['id' => $goods_id]);

        $redis  = new Predis();
        $key    = 'SECKILL:GOODS:'.$info['id'];
        $length = $redis->llen($key);
        if ($length) {
            return errorMsg('已经设置过秒杀库存，请勿重复设置');
        }
        for ($i = 0;$i <= $info['number']; $i++) {
            $redis->rpush($key, $i);
        }

        return successMsg();
    }

    /**
     * 将用户也存入队列中
     * 没有进行用户过滤，同一用户多次请求也会入队
     * @param $user_id
     * @return bool
     */
    public static function userList($user_id)
    {
        $redis  = new Predis();
        $key    = 'SECKILL:USER';
        $length = $redis->llen($key);

        # 判断队列数（防止队列数据过大，redis服务器炸掉）
        if ($length >= self::$limit) {
            return false;
        }

        # 添加数据
        $redis->rpush($key, $user_id);
        return true;
    }
}
