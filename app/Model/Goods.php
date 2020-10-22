<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/30
 * Time: 18:10
 */

namespace App\Model;

class Goods extends BaseModel
{

    protected $table = 'goods';

    /**
     * 获取商品信息
     * @param $param
     * @return array
     */
    public static function getInfo($param)
    {
        $where = Array();
        if (array_key_exists('id',$param) && !empty($param['id'])) {
            $where[] = ['id','=',$param['id']];
        }

        $info  = self::where($where)->first();
        if ($info) {
            return $info->toArray();
        }
        return [];
    }
}
