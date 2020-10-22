<?php

namespace App\Console\Commands;

use App\Model\Role;
use App\Model\Menu;
use App\Model\Admin;
use App\Service\AdminService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AdminInitialize extends Command
{

    # 运行命令示例：php artisan adminInitialize admin --password=123456

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adminInitialize {username} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'background management system initialization';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $username = $this->argument('username');
        $password = $this->option('password');
        if (empty($username) || empty($password)) {
            $this->info('参数错误');
            return false;
        }

        # 验证是否有管理员
        $count = Admin::getCount();
        if ($count) {
            $this->info('管理员已存在，请勿重复初始化');
            return false;
        }

        DB::beginTransaction();
        try {
            # 初始化角色
            $role['name'] = '超级管理员';
            $role['is_admin'] = 1;
            $role['created_by'] = 0;
            $roleId = Role::addRole($role);

            # 初始化超管员
            AdminService::addAdmin($username,$password,$roleId);

            # 初始化菜单
            $permission = [
                ['name' => '管理员','alias' => 'admin','icon' => ''],
                ['name' => '角色管理','alias' => 'role','icon' => ''],
                ['name' => '菜单管理','alias' => 'menus','icon' => '']
            ];
            $log = [
                ['name' => '错误日志','alias' => 'errorLog','icon' => ''],
                ['name' => '登录日志','alias' => 'loginLog','icon' => ''],
                ['name' => '访问日志','alias' => 'requestLog','icon' => '']
            ];
            $menu = [
                ['name' => '系统首页','alias' => 'dashboard','icon' => 'el-icon-lx-home','children' => []],
                ['name' => '权限管理','alias' => 'permission','icon' => 'el-icon-setting','children' => $permission],
                ['name' => '日志管理','alias' => 'log','icon' => 'el-icon-document-remove','children' => $log]
            ];
            foreach ($menu as $val) {
                $data['parent_id'] = 0;
                $data['name']      = $val['name'];
                $data['alias']     = $val['alias'];
                $data['icon']      = $val['icon'];
                $data['sort']      = 0;
                $pid = Menu::addMenu($data);
                $children = $val['children'];
                if (count($children)) {
                    foreach ($children as $k=>$v) {
                        $data['parent_id'] = $pid;
                        $data['name']      = $v['name'];
                        $data['alias']     = $v['alias'];
                        $data['icon']      = $v['icon'];
                        $data['sort']      = $k;
                        Menu::addMenu($data);
                    }
                }
            }
            DB::commit();
            $this->info('初始化成功');
            return true;
        }catch (\Exception $e) {
            DB::rollBack();
            $this->info('初始化失败：'.$e->getMessage());
            return true;
        }
    }
}
