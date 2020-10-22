<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/19
 * Time: 17:44
 */

namespace App\Model;

class Role extends BaseModel
{

    protected $table = 'role';

    /**
     * 添加角色
     * @param $info
     * @return mixed
     */
    public static function addRole($info)
    {
        $time = time();
        $info['updated_at'] = $time;
        $info['created_at'] = $time;

        return self::insertGetId($info);
    }

    /**
     * 获取角色信息
     * @param $param
     * @return array
     */
    public static function getInfo($param)
    {
        $where = Array();
        if (array_key_exists('id',$param) && !empty($param['id'])) {
            $where[] = ['id','=',$param['id']];
        }
        if (array_key_exists('name',$param) && !empty($param['name'])) {
            $where[] = ['name','=',$param['name']];
        }

        $field = ['id','name','status','is_admin'];
        $info  = self::where($where)->select($field)->first();
        if ($info) {
            return $info->toArray();
        }
        return [];
    }

    /**
     * 获取角色列表
     * @param $param
     * @param $limit
     * @return mixed
     */
    public static function getRoleList($param, $limit)
    {
        $where = Array();
        if (array_key_exists('is_admin',$param) && $param['is_admin'] != null) {
            $where[] = ['is_admin','=',$param['is_admin']];
        }

        $field = ['id','name','status','is_admin','created_at'];
        $list  = self::where($where)
                ->select($field)
                ->orderBy('is_admin','desc')
                ->paginate($limit)
                ->toArray();

        return $list;
    }

    /**
     * 编辑角色
     * @param $id
     * @param $info
     * @return mixed
     */
    public static function editRole($id, $info)
    {
        $info['updated_at'] = time();

        return self::where(['id' => $id])->update($info);
    }

    /**
     * 删除角色
     * @param $id
     * @return mixed
     */
    public static function delRole($id)
    {
        return self::where(['id' => $id])->delete();
    }
}
