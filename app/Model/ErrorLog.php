<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/29
 * Time: 18:27
 */

namespace App\Model;

class ErrorLog extends BaseModel
{

    protected $table = 'error_log';

    /**
     * 获取错误日志列表
     * @param $where
     * @param $limit
     * @return mixed
     */
    public static function getErrolLog($where, $limit)
    {
        $data = self::where($where)->orderBy('created_at', 'desc')->paginate($limit)->toArray();

        return $data;
    }

    /**
     * 添加错误日志
     * @param $info
     * @return mixed
     */
    public static function addInfo($info)
    {
        $info['updated_at'] = time();

        return self::insert($info);
    }

    /**
     * 编辑错误日志
     * @param $id
     * @param $status
     * @return mixed
     */
    public static function editError($id, $status)
    {
        $info['status'] = $status;
        $info['updated_at'] = time();

        return self::where(['id' => $id])->update($info);
    }

    /**
     * 统计错误日志数量
     * @param $where
     * @return mixed
     */
    public static function getErrorCount($where)
    {
        $count = self::where($where)->count();

        return $count;
    }
}
