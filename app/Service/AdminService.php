<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/18
 * Time: 15:38
 */

namespace App\Service;

use App\Model\Role;
use App\Model\Admin;
use App\Model\AdminLoginLog;

class AdminService
{

    /**
     * 根据Id获取管理员信息
     * @param $id
     * @return array|bool
     */
    public static function getInfoById($id)
    {
        if ($id <= 0) {
            return [];
        }
        return Admin::getInfo(['id' => $id]);
    }

    /**
     * 管理员登录验证
     * @param $username
     * @param $password
     * @return mixed
     */
    public static function loginCheck($username, $password)
    {
        if (empty($username) || empty($password)) {
            return errorMsg();
        }

        # 获取管理员信息
        $where = ['username' => $username];
        $info  = Admin::getInfo($where);
        if (!$info) {
            return errorMsg('账号或密码错误');
        }

        # 验证密码
        $encrypt = md5($password . $info['safe']);
        if ($encrypt != $info['password']) {
            return errorMsg('账号或密码错误');
        }

        # 验证管理员状态
        if ($info['status'] != 1) {
            return errorMsg('账号异常');
        }

        # 验证角色状态
        if ($info['role_status'] != 1) {
            return errorMsg('账号异常');
        }

        # 获取用户拥有权限
        $permission = RoleService::getPermission($info['role_id'],$info['is_admin']);
        if (!count($permission['list'])) {
            return errorMsg('暂无权限');
        }

        unset($info['password']);
        unset($info['safe']);

        return successMsg($info);
    }

    /**
     * 记录管理员登录信息
     * @param $info
     */
    public static function saveLoginInfo($info)
    {
        # 记录登录信息
        $time = time();
        $info['time'] = $time;
        AdminLoginLog::saveInfo($info);

        # 更新登录信息
        $update['last_login'] = $time;
        $update['last_ip']    = $info['ip'];
        Admin::where(['username' => $info['username']])->increment('login_num', 1, $update);
    }

    /**
     * 添加管理员
     * @param $username
     * @param $password
     * @param $roleId
     * @param int $status
     * @return mixed
     */
    public static function addAdmin($username,$password,$roleId,$status = 1)
    {
        # 检测是否重名
        $info = Admin::getInfo(['username' => $username]);
        if ($info) {
            return errorMsg('此管理员已存在，请勿重复添加');
        }

        $admin['username'] = $username;
        $admin['role_id']  = $roleId;
        $safe = randString();
        $admin['safe'] = $safe;
        $encrypt = md5($password . $safe);
        $admin['password'] = $encrypt;
        $admin['status']   = $status;

        $adminId = Admin::addAdmin($admin);
        if ($adminId) {
            return successMsg(['id' => $adminId]);
        }
        return errorMsg('操作失败');
    }

    /**
     * 编辑管理员
     * @param $id
     * @param $roleId
     * @param $status
     * @param $password
     * @return mixed
     */
    public static function editAdmin($id,$roleId,$status,$password)
    {
        # 验证管理员
        $info = Admin::getInfo(['id' => $id]);
        if (!$info) {
            return errorMsg();
        }
        if ($info['is_admin'] == 1) {
            return errorMsg('无权修改超级管理员');
        }

        # 验证角色
        $role = Role::getInfo(['id' => $roleId]);
        if (!$role) {
            return errorMsg();
        }
        if ($role['is_admin'] == 1) {
            return errorMsg('无权将角色设置为超级管理员');
        }

        if ($password) {
            $encrypt = md5($password . $info['safe']);
            $admin['password'] = $encrypt;
        }
        $admin['status']  = $status;
        $admin['role_id'] = $roleId;

        $result = Admin::editAdmin($id,$admin);
        if ($result) {
            return successMsg();
        }
        return errorMsg('操作失败');
    }

    /**
     * 删除管理员
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public static function delAdminById($id)
    {
        # 获取被删除的管理员信息
        $info = Admin::getInfo(['id' => $id]);
        if ($info && $info['is_admin'] == 1) {
            return errorMsg('无权删除超级管理员');
        }

        $res = Admin::delAdmin($id);
        if ($res) {
            return successMsg();
        }
        return errorMsg('删除失败，请稍后重试');
    }

    /**
     * 修改管理员密码
     * @param $id
     * @param $oldPassword
     * @param $password
     * @return mixed
     */
    public static function changePassword($id, $oldPassword, $password)
    {
        # 验证原始密码
        $info = Admin::getInfo(['id' => $id]);
        if (!$info) {
            return errorMsg();
        }
        $encrypt = md5($oldPassword . $info['safe']);
        if ($encrypt != $info['password']) {
            return errorMsg('旧密码错误');
        }

        $safe = randString();
        $admin['safe'] = $safe;
        $admin['password'] = md5($password . $safe);
        $result = Admin::editAdmin($id,$admin);
        if ($result) {
            return successMsg();
        }
        return errorMsg('修改失败，请稍后重试');
    }
}
