<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/19
 * Time: 9:29
 */

namespace App\Extend;

use Illuminate\Support\Facades\Redis;

class Predis
{

    protected static $redis;

    public function __construct()
    {
        self::$redis = Redis::connection('default');
    }


    /****************************************************************************************
     ****************************************string******************************************
     ****************************************************************************************/


    /**
     * 设置缓存
     * @param $key
     * @param $value
     * @param int $time
     * @return bool|int|mixed
     */
    public function set($key, $value, $time = 0)
    {
        if (empty($key) || empty($value)) {
            return false;
        }

        # 如果是数组，进行编码
        if (is_array($value)) {
            $value = json_encode($value,JSON_UNESCAPED_UNICODE);
        }

        if ($time) {
            # 以秒为单位的过期时间
            return static::_setex($key, $value, (int)$time);
        } else {
            # 不设置过期时间
            return static::$redis->set($key, $value);
        }
    }

    /**
     * 设置以秒为过期时间单位的缓存
     * @param $key
     * @param $value
     * @param $time
     * @return int
     */
    private static function _setex($key, $value, $time)
    {
        return static::$redis->setex($key, $time, $value);
    }

    /**
     * 获取缓存
     * @param $key
     * @return bool|string
     */
    public function get($key)
    {
        if (empty($key)) {
            return false;
        }

        return static::$redis->get($key);
    }

    /**
     * 删除指定缓存
     * @param $key
     * @return bool|int
     */
    public function del($key)
    {
        if (empty($key)) {
            return false;
        }

        return static::$redis->del($key);
    }

    /**
     * 设置有效期
     * @param $key
     * @param $time
     * @return bool|int
     */
    public function expire($key, $time = 0)
    {
        if (empty($key) || $time <= 0) {
            return false;
        }

        return static::$redis->expire($key,$time);
    }

    /**
     * 判断缓存是否在Redis内
     * @param $key
     * @return bool|int
     */
    public function exists($key)
    {
        if (empty($key)) {
            return false;
        }

        return static::$redis->exists($key);
    }


    /****************************************************************************************
     ******************************************hash******************************************
     ****************************************************************************************/


    /**
     * 设置hash值
     * @param $key
     * @param $field
     * @param $value
     * @return bool|int
     */
    public function hset($key, $field, $value)
    {
        if (empty($key) || empty($field) || empty($value)) {
            return false;
        }

        return static::$redis->hset($key, $field, $value);
    }

    /**
     * 获取指定字段hash值
     * @param $key
     * @param $field
     * @return bool|string
     */
    public function hget($key, $field)
    {
        if (empty($key) || empty($field)) {
            return false;
        }

        return static::$redis->hget($key, $field);
    }

    /**
     * 获取指定键名所有值
     * @param $key
     * @return array|bool
     */
    public function hgetall($key)
    {
        if (empty($key)) {
            return false;
        }

        return static::$redis->hgetall($key);
    }

    /**
     * 删除指定字段hash值
     * @param $key
     * @param $fields
     * @return bool|int
     */
    public function hdel($key, $fields)
    {
        if (empty($key) || !count($fields)) {
            return false;
        }

        return static::$redis->hdel($key, $fields);
    }

    /**
     * 判断字段是否存在于指定键名中
     * @param $key
     * @param $field
     * @return bool|int
     */
    public function hexists($key, $field)
    {
        if (empty($key) || empty($field)) {
            return false;
        }

        return static::$redis->hexists($key, $field);
    }

    /**
     * 返回hash列表key的长度
     * @param $key
     * @return bool|int
     */
    public function hlen($key)
    {
        if (empty($key)) {
            return false;
        }

        return static::$redis->hlen($key);
    }

    /**
     * 设置hash值,当且仅当域 field不存在时
     * @param $key
     * @param $field
     * @param $value
     * @return bool|int
     */
    public function hsetnx($key, $field, $value)
    {
        if (empty($key) || empty($field)) {
            return false;
        }

        return static::$redis->hsetnx($key, $field);
    }


    /****************************************************************************************
     ******************************************list******************************************
     ****************************************************************************************/


    /**
     * 将一个或多个值value插入到列表key的表头
     * @param $key
     * @param $value
     * @return bool|int
     */
    public function lpush($key, $value)
    {
        if (empty($key) || empty($value)) {
            return false;
        }

        return static::$redis->lpush($key, $value);
    }

    /**
     * 移除头元素并返回列表key的头元素
     * @param $key
     * @return bool|string 当key不存在时返回nil
     */
    public function lpop($key)
    {
        if (empty($key)) {
            return false;
        }

        return static::$redis->lpop($key);
    }

    /**
     * 将一个或多个值value插入到列表key的表尾(最右边)
     * 如果key不存在，一个空列表会被创建并执行RPUSH操作
     * @param $key
     * @param $value
     * @return bool|int
     */
    public function rpush($key, $value)
    {
        if (empty($key) || empty($value)) {
            return false;
        }

        return static::$redis->rpush($key, $value);
    }

    /**
     * 移除尾元素并返回列表key的尾元素
     * @param $key
     * @return bool|string 当key不存在时，返回 nil
     */
    public function rpop($key)
    {
        if (empty($key)) {
            return false;
        }

        return static::$redis->rpop($key);
    }

    /**
     * @param $key
     * @return bool|int
     */
    public function llen($key)
    {
        if (empty($key)) {
            return false;
        }

        return static::$redis->llen($key);
    }

    /**
     * 查询出list列表中的指定位置的值
     * @param $key
     * @param $start
     * @param $end
     * @return array|bool
     */
    public function lrange($key, $start, $end)
    {
        if (empty($key)) {
            return false;
        }
        return static::$redis->lrange($key, $start,$end);
    }

    /**
     * 获取list列表中所有值
     * @param $key
     * @return array|bool
     */
    public function lgetall($key)
    {
        if (empty($key)) {
            return false;
        }

        return static::$redis->lrange($key, 0, -1);
    }


    /****************************************************************************************
     *****************************************递增递减****************************************
     ****************************************************************************************/


    /**
     * 对值的递增
     * @param $key
     * @param int $step
     * @return bool|int
     */
    public function incr($key, $step = 1)
    {
        if (empty($key)) {
            return false;
        }
        if ($step <= 0) {
            return false;
        }

        # 以指定值递增
        return static::$redis->incrby($key, $step);
    }

    /**
     * 对值的递减
     * @param $key
     * @param int $step
     * @return bool|int
     */
    public function decr($key, $step = 1)
    {
        if (empty($key)) {
            return false;
        }
        if ($step <= 0) {
            return false;
        }

        # 以指定值递减
        return static::$redis->decrby($key, $step);
    }
}
