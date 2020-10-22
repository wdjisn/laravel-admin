<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/18
 * Time: 15:36
 */

namespace App\Model;

class Admin extends BaseModel
{

    protected $table = 'admin';

    /**
     * 获取管理员信息
     * @param $param
     * @return array
     */
    public static function getInfo($param)
    {
        $where = Array();
        if (array_key_exists('id',$param) && !empty($param['id'])) {
            $where[] = ['a.id','=',$param['id']];
        }
        if (array_key_exists('username',$param) && !empty($param['username'])) {
            $where[] = ['a.username','=',$param['username']];
        }
        if (array_key_exists('username',$param) && !empty($param['username'])) {
            $where[] = ['a.username','=',$param['username']];
        }

        $field = ['a.id','a.username','a.password','a.safe','a.role_id','a.status','r.name as role_name','r.status as role_status','r.is_admin'];
        $info  = self::from('admin as a')
                 ->leftjoin('role as r','a.role_id','=','r.id')
                 ->select($field)
                 ->where($where)
                 ->first();
        if ($info) {
            return $info->toArray();
        }
        return [];
    }

    /**
     * 获取管理员人数
     * @param $param
     * @return mixed
     */
    public static function getCount($param = Array())
    {
        $where = Array();
        if (array_key_exists('role_id',$param) && !empty($param['role_id'])) {
            $where[] = ['role_id','=',$param['role_id']];
        }

        return self::where($where)->count();
    }

    /**
     * 添加管理员
     * @param $info
     * @return mixed
     */
    public static function addAdmin($info)
    {
        $time = time();
        $info['updated_at'] = $time;
        $info['created_at'] = $time;

        return self::insertGetId($info);
    }

    /**
     * 编辑管理员
     * @param $id
     * @param $info
     * @return mixed
     */
    public static function editAdmin($id, $info)
    {
        $info['updated_at'] = time();

        return self::where(['id' => $id])->update($info);
    }

    /**
     * 获取管理员列表
     * @param $param
     * @param $limit
     * @return mixed
     */
    public static function getAdminList($param, $limit)
    {
        $where = Array();
        if (array_key_exists('username',$param) && !empty($param['username'])) {
            $where[] = ['a.username','=',$param['username']];
        }

        $field = ['a.id','a.username','a.role_id','r.name as role_name','r.is_admin','a.status','a.last_login','a.last_ip','a.created_at'];
        $list  = self::from('admin as a')
                 ->where($where)
                 ->select($field)
                 ->leftjoin('role as r','a.role_id','=','r.id')
                 ->orderBy('a.id','asc')
                 ->paginate($limit)
                 ->toArray();

        return $list;
    }

    /**
     * 删除管理员
     * @param $id
     * @return mixed
     */
    public static function delAdmin($id)
    {
        return self::where(['id' => $id])->delete();
    }
}
