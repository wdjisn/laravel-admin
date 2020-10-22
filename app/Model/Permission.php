<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/19
 * Time: 17:56
 */

namespace App\Model;

class Permission extends BaseModel
{

    protected $table = 'permission';

    /**
     * 获取角色用于的菜单权限
     * @param $roleId
     * @return mixed
     */
    public static function getPermission($roleId)
    {
        $where = ['m.status' => 1,'p.role_id' => $roleId];
        $field = ['m.id','m.parent_id','m.name','m.alias','m.icon'];
        $list  = Permission::from('permission as p')
                ->leftjoin('menu as m','p.menu_id','=','m.id')
                ->where($where)
                ->select($field)
                ->orderBy('m.sort')
                ->get()
                ->toArray();

        return $list;
    }

    /**
     * 删除角色权限
     * @param $id
     * @return mixed
     */
    public static function delPermissionByRole($id)
    {
        return  self::where(['role_id' => $id])->delete();
    }

    /**
     * 删除菜单权限
     * @param $id
     * @return mixed
     */
    public static function delPermissionByMenu($id)
    {
        return  self::where(['menu_id' => $id])->delete();
    }
}
