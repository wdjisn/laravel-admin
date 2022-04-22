<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/19
 * Time: 17:45
 */

namespace App\Service;

use App\Model\Menu;
use App\Model\Role;
use App\Model\Admin;
use App\Model\Permission;
use Illuminate\Support\Facades\DB;

class RoleService
{

    /**
     * 根据Id获取角色信息
     * @param $id
     * @return array|bool
     */
    public static function getInfoById($id)
    {
        $data = Array();
        if ($id <= 0) {
            return $data;
        }
        $info = Role::getInfo(['id' => $id]);
        if ($info) {
            $data = $info;
            $permission = Permission::getPermission($id);
            $parent = [];
            foreach ($permission as $val) {
                if ($val['parent_id'] == 0) {
                    $parent[] = $val['id'];
                }
            }
            $menu = [];
            foreach ($permission as $value) {
                if ($value['parent_id'] != 0) {
                    $key = array_search($value['parent_id'],$parent);
                    if ($key !== false) {
                        unset($parent[$key]);
                    }
                    $menu[] = $value['id'];
                }
            }
            $data['menu'] = array_merge($parent,$menu);
            $data['menu'] = array_unique($data['menu']);
        }
        return $data;
    }

    /**
     * 获取角色拥有的菜单权限
     * @param $roleId
     * @param $isAdmin
     * @return |null
     */
    public static function getPermission($roleId, $isAdmin)
    {
        if ($isAdmin == 1) {
            # 超级管理员可以获取所有权限
            $list = Menu::getMenus(['status' => 1]);
        } else {
            $list = Permission::getPermission($roleId);
        }
        $tree = getTree($list);

        return ['list' => $list,'tree' => $tree];
    }

    /**
     * 添加角色
     * @param $name
     * @param $menuIds
     * @param $createdBy
     * @param int $status
     * @return mixed
     */
    public static function addRole($name, $menuIds, $createdBy, $status = 1)
    {
        # 验证参数
        if (!$name) {
            return error('请输入角色名称');
        }
        if (!$menuIds) {
            return error('请选择角色权限');
        }

        # 检测是否重名
        $info = Role::getInfo(['name' => $name]);
        if ($info) {
            return error('此角色已存在，请勿重复添加');
        }

        $menuIds = array_unique($menuIds);
        DB::beginTransaction();
        try {
            $role['name'] = $name;
            $role['status'] = $status;
            $role['created_by'] = $createdBy;
            $roleId = Role::addRole($role);

            $permission = Array();
            foreach ($menuIds as $val) {
                $tmp = Array();
                $tmp['role_id']    = $roleId;
                $tmp['menu_id']    = $val;
                $tmp['created_at'] = time();
                $permission[] = $tmp;
            }
            Permission::insert($permission);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return error('操作失败，请稍后重试');
        }
        return success();
    }

    /**
     * 编辑角色
     * @param $id
     * @param $name
     * @param $menuIds
     * @param $status
     * @return mixed
     */
    public static function editRole($id,$name,$menuIds,$status)
    {
        if ($id <= 0) {
            return error();
        }
        if (!$name) {
            return error('请输入角色名称');
        }
        if (!$menuIds) {
            return error('请选择角色权限');
        }

        # 获取被删除的角色信息
        $info = Role::getInfo(['id' => $id]);
        if (!$info) {
            return error();
        }
        if ($info && $info['is_admin'] == 1) {
            return error('无权修改超级管理员');
        }

        DB::beginTransaction();
        try {
            $role['name'] = $name;
            $role['status'] = $status;
            Role::editRole($id,$role);

            Permission::delPermissionByRole($id);

            $permission = Array();
            foreach ($menuIds as $val) {
                $tmp = Array();
                $tmp['role_id']    = $id;
                $tmp['menu_id']    = $val;
                $tmp['created_at'] = time();
                $permission[] = $tmp;
            }
            Permission::insert($permission);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return error('操作失败，请稍后重试');
        }
        return success();
    }

    /**
     * 删除角色
     * @param $id
     * @return mixed
     */
    public static function delRoleById($id)
    {
        if ($id <= 0) {
            return error();
        }

        # 获取被删除的角色信息
        $info = Role::getInfo(['id' => $id]);
        if ($info && $info['is_admin'] == 1) {
            return error('无权删除超级管理员');
        }

        # 验证使用该角色的管理员数量
        $adminCount = Admin::getCount(['role_id' => $id]);
        if ($adminCount) {
            return error('有管理员使用此角色，无法删除');
        }

        DB::beginTransaction();
        try {
            Role::delRole($id);
            Permission::delPermissionByRole($id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return error('操作失败，请稍后重试');
        }
        return success();
    }
}
