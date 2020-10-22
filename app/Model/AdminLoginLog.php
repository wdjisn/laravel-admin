<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/18
 * Time: 17:00
 */

namespace App\Model;

class AdminLoginLog extends BaseModel
{

    protected $table   = 'admin_login_log';

    /**
     * 保存登录信息
     * @param array $data
     * @return mixed
     */
    public static function saveInfo($data = [])
    {
        $info = [
            'admin_id'   => $data['id'],
            'login_ip'   => $data['ip'],
            'created_at' => $data['time'],
        ];
        return self::insertGetId($info);
    }

    /**
     * 获取登录日志
     * @param $where
     * @param $limit
     * @return mixed
     */
    public static function getLoginLog($where, $limit)
    {
        $data = self::from('admin_login_log as all')
                ->where($where)
                ->select('all.id','a.username','r.name as role_name','all.login_ip','all.created_at')
                ->leftjoin('admin as a', 'a.id', '=', 'all.admin_id')
                ->leftjoin('role as r', 'r.id', '=', 'a.role_id')
                ->orderBy('all.id', 'desc')
                ->paginate($limit)
                ->toArray();

        return $data;
    }
}
