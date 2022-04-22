<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/16
 * Time: 18:04
 */

namespace App\Http\Controllers\Admin;

use App\Model\Role;
use App\Service\RoleService;

class RoleController extends BaseController
{

    /**
     * 获取角色列表
     */
    public function getRoleList()
    {
        $where['is_admin'] = $this->requestArr['is_admin'] ?? null;
        $perPage = $this->requestArr['per_page'] ?? 15;

        $data = Role::getRoleList($where,$perPage);
        foreach ($data['data'] as &$val) {
            $val['status'] = $val['status'] == 1 ? true : false;
        }

        jSuccess($data);
    }

    /**
     * 获取角色详情
     */
    public function getRoleInfo()
    {
        $id = $this->requestArr['id'];

        $info = RoleService::getInfoById($id);

        jSuccess($info);
    }

    /**
     * 添加角色
     */
    public function createRole()
    {
        $name    = $this->requestArr['name'];
        $menuIds = $this->requestArr['menu_ids'];
        $status  = $this->requestArr['status'] == 1 ? 1 : 0;

        $result = RoleService::addRole($name,$menuIds,$this->userId,$status);
        if (!$result['status']) {
            jError($result['msg']);
        }
        jSuccess();
    }

    /**
     * 编辑角色
     */
    public function editRole()
    {
        $id      = $this->requestArr['id'];
        $name    = $this->requestArr['name'];
        $menuIds = $this->requestArr['menu_ids'];
        $status  = $this->requestArr['status'] == 1 ? 1 : 0;

        $result = RoleService::editRole($id,$name,$menuIds,$status);
        if (!$result['status']) {
            jError($result['msg']);
        }
        jSuccess();
    }

    /**
     * 快捷修改角色
     */
    public function quickEditRole()
    {
        $id = $this->requestArr['id'];
        $data['status'] = $this->requestArr['status'] == 1 ? 1 : 0;

        $result = Role::editRole($id,$data);
        if ($result) {
            jSuccess();
        }
        jError($result['msg']);
    }

    /**
     * 删除角色
     */
    public function deleteRole()
    {
        $id = $this->requestArr['id'];

        $result = RoleService::delRoleById($id);
        if (!$result['status']) {
            jError($result['msg']);
        }
        jSuccess();
    }
}
