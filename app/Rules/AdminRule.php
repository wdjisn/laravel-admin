<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/18
 * Time: 15:39
 */

namespace App\Rules;

class AdminRule extends BaseRule
{

    /**
     * 自定义验证规则
     * @var array
     */
    protected $rule = [
        'username'         => 'required',
        'password'         => 'required',
        'old_password'     => 'required',
        'confirm_password' => 'required|same:password',
        'role_id'          => 'required|integer|min:2'
    ];

    /**
     * 自定义验证信息
     * @var array
     */
    protected $message = [
        'username.required'         => '请输入账号',
        'password.required'         => '请输入密码',
        'old_password.required'     => '请输入旧密码',
        'confirm_password.required' => '请输入确认密码',
        'confirm_password.same'     => '密码和确认密码不一致',
        'role_id.required'          => '请选择角色',
        'role_id.min'               => '请选择角色',
    ];

    /**
     * 自定义场景
     * @var array
     */
    protected $scene = [
        'login' => "username,password",
        'add'   => "username,password,confirm_password,role_id",
        'change_password' => 'old_password,password,confirm_password'
    ];
}
