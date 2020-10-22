<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/16
 * Time: 17:51
 */

namespace App\Http\Controllers\Admin;

use App\Model\Admin;
use App\Rules\AdminRule;
use App\Service\AdminService;

class AdminController extends BaseController
{

    /**
     * 获取管理员列表
     */
    public function getAdminList()
    {
        $perPage  = $this->requestArr['per_page'] ?? 15;
        $where['username'] = $this->requestArr['username'];

        $data = Admin::getAdminList($where, $perPage);
        foreach ($data['data'] as &$val) {
            $val['status'] = $val['status'] == 1 ? true : false;
        }

        successReturn($data);
    }

    /**
     * 获取管理员详情
     */
    public function getAdminInfo()
    {
        $id = $this->requestArr['id'];

        $info = Array();
        $data = AdminService::getInfoById($id);
        if ($data) {
            $info['id']       = $data['id'];
            $info['status']   = $data['status'];
            $info['role_id']  = $data['role_id'];
            $info['username'] = $data['username'];
        }

        successReturn($info);
    }

    /**
     * 添加管理员
     */
    public function createAdmin()
    {
        $username = $this->requestArr['username'];
        $password = $this->requestArr['password'];
        $roleId   = $this->requestArr['role_id'];
        $status   = $this->requestArr['status'] == 1 ? 1 : 0;
        $confirmPassword = $this->requestArr['confirm_password'];

        # 验证参数
        $adminRule = new AdminRule();
        $post = compact('username','password');
        $post['role_id'] = $roleId;
        $post['confirm_password'] = $confirmPassword;
        if (!$adminRule->scene('add')->check($post)) {
            errorReturn($adminRule->getError());
        }

        $result = AdminService::addAdmin($username,$password,$roleId,$status);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }
        successReturn();
    }

    /**
     * 编辑管理员
     */
    public function editAdmin()
    {
        $id       = $this->requestArr['id'];
        $password = $this->requestArr['password'];
        $roleId   = $this->requestArr['role_id'];
        $status   = $this->requestArr['status'] == 1 ? 1 : 0;

        $result = AdminService::editAdmin($id,$roleId,$status,$password);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }
        successReturn();
    }

    /**
     * 快捷修改管理员
     */
    public function quickEditAdmin()
    {
        $id = $this->requestArr['id'];
        $data['status'] = $this->requestArr['status'] == 1 ? 1 : 0;

        $result = Admin::editAdmin($id,$data);
        if ($result) {
            successReturn();
        }
        errorReturn($result['msg']);
    }

    /**
     * 删除管理员
     * @throws \Exception
     */
    public function deleteAdmin()
    {
        $id = $this->requestArr['id'];

        if ($id == $this->userId) {
            errorReturn('无法自我删除');
        }

        $result = AdminService::delAdminById($id);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }
        successReturn();
    }
}
