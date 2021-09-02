<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/16
 * Time: 17:39
 */

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->namespace('Admin')->middleware(['requestLog'])->group(function () {
    Route::post('/login', 'LoginController@login');                             # 登录
    Route::get('/qiniu/token','LoginController@getQiniuToken');                 # 获取七牛token
    Route::delete('/login', 'IndexController@loginOut');                        # 退出
    Route::put('/change/password', 'IndexController@changePassword');           # 修改密码
    Route::get('/permission', 'IndexController@getPermission');                 # 获取权限
    Route::post('/send/code', 'IndexController@sendCode');                      # 发送短信验证码
    Route::post('/upload/file', 'IndexController@uploadFile');                  # form表单上传文件
    Route::post('/upload/base64/image', 'IndexController@uploadBase64Image');   # base64字符串上传图片
    Route::post('/send/notify','IndexController@batchSendNotify');              # 批量发送短信通知(使用队列)
    Route::post('/create/seckill','IndexController@createSeckill');             # 创建秒杀
    Route::get('/user/seckill','IndexController@userSeckill');                  # 用户秒杀
    Route::get('/seckill/result','IndexController@seckillResult');              # 显示秒杀结果
    Route::post('/create/live','IndexController@createLive');                   # 创建直播流
    Route::get('/lives','IndexController@getLiveList');                         # 获取直播列表

    Route::get('/admins', 'AdminController@getAdminList');                      # 获取管理员列表
    Route::get('/admin', 'AdminController@getAdminInfo');                       # 获取管理员详情
    Route::post('/admin', 'AdminController@createAdmin');                       # 添加管理员
    Route::put('/admin', 'AdminController@editAdmin');                          # 编辑管理员
    Route::patch('/admin', 'AdminController@quickEditAdmin');                   # 快捷修改管理员
    Route::delete('/admin', 'AdminController@deleteAdmin');                     # 删除管理员

    Route::get('/roles', 'RoleController@getRoleList');                         # 获取角色列表
    Route::get('/role', 'RoleController@getRoleInfo');                          # 获取角色详情
    Route::post('/role', 'RoleController@createRole');                          # 添加角色
    Route::put('/role', 'RoleController@editRole');                             # 编辑角色
    Route::patch('/role', 'RoleController@quickEditRole');                      # 快捷修改角色
    Route::delete('/role', 'RoleController@deleteRole');                        # 删除角色

    Route::get('/menus', 'MenuController@getMenuList');                         # 获取菜单列表
    Route::get('/menu/tree', 'MenuController@getMenuTree');                     # 获取树形菜单
    Route::get('/menu', 'MenuController@getMenuInfo');                          # 获取菜单详情
    Route::post('/menu', 'MenuController@createMenu');                          # 添加菜单
    Route::put('/menu', 'MenuController@editMenu');                             # 编辑菜单
    Route::patch('/menu', 'MenuController@quickEditMenu');                      # 快捷修改菜单
    Route::delete('/menu', 'MenuController@deleteMenu');                        # 删除菜单

    Route::get('/error/logs', 'LogController@getErrorLog');                     # 获取错误日志
    Route::put('/error/log', 'LogController@editError');                        # 编辑错误日志
    Route::get('/login/logs', 'LogController@getLoginLog');                     # 获取登录日志
    Route::get('/request/logs', 'LogController@getRequestLog');                 # 获取访问日志
});
