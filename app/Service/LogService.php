<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/25
 * Time: 15:26
 */

namespace App\Service;

use App\Model\ErrorLog;
use App\Model\AdminLoginLog;

class LogService
{

    /**
     * 获取登录日志
     * @param $param
     * @param $limit
     * @return mixed
     */
    public static function getLoginLog($param, $limit)
    {
        $where = Array();
        if (array_key_exists('username',$param) && !empty($param['username'])) {
            $where[] = ['a.username', '=' , $param['username']];
        }
        if (array_key_exists('start',$param) && !empty($param['start'])) {
            $startTime = strtotime($param['start']);
            $where[] = ['all.created_at', '>=' , $startTime];
        }
        if (array_key_exists('end',$param) && !empty($param['end'])) {
            $endTime = strtotime($param['end']);
            $where[] = ['all.created_at', '<=' , $endTime];
        }

        $result = AdminLoginLog::getLoginLog($where,$limit);
        return $result;
    }

    /**
     * 获取错误日志
     * @param $param
     * @param $limit
     * @return mixed
     */
    public static function getErrorLog($param, $limit)
    {
        $where = Array();
        if (array_key_exists('status',$param) && $param['status'] !== null) {
            $where[] = ['status', '=' , $param['status']];
        }
        if (array_key_exists('start',$param) && !empty($param['start'])) {
            $startTime = strtotime($param['start']);
            $where[] = ['created_at', '>=' , $startTime];
        }
        if (array_key_exists('end',$param) && !empty($param['end'])) {
            $endTime = strtotime($param['end']);
            $where[] = ['created_at', '<=' , $endTime];
        }

        $result = ErrorLog::getErrolLog($where,$limit);
        return $result;
    }

    /**
     * 统计错误日志数量
     * @param $param
     * @return mixed
     */
    public static function getErrorCount($param)
    {
        $where = Array();
        if (array_key_exists('status',$param) && $param['status'] !== null) {
            $where[] = ['status', '=' , $param['status']];
        }

        $result = ErrorLog::getErrorCount($where);
        return $result;
    }
}
