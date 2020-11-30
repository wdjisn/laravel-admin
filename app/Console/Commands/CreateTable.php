<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTable extends Command
{

    # 运行命令示例：php artisan createTable

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createTable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create data table';

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
        try {
            DB::statement("CREATE TABLE `admin` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `username` varchar(255) NOT NULL COMMENT '名称',
                                  `password` varchar(255) NOT NULL COMMENT '密码',
                                  `safe` varchar(255) NOT NULL COMMENT '安全码',
                                  `role_id` int(111) NOT NULL DEFAULT '0' COMMENT '角色id',
                                  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态，是否禁用 : 0否，1是',
                                  `login_num` int(11) NOT NULL DEFAULT '0' COMMENT '登录次数',
                                  `last_login` int(11) DEFAULT '0' COMMENT '最后登录时间',
                                  `last_ip` varchar(255) DEFAULT NULL COMMENT '最后登录ip',
                                  `updated_at` int(11) DEFAULT '0',
                                  `created_at` int(11) NOT NULL,
                                  PRIMARY KEY (`id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员表';");
            DB::statement("CREATE TABLE `admin_login_log` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `admin_id` int(11) NOT NULL COMMENT '管理员id',
                                  `login_ip` varchar(255) NOT NULL COMMENT '登录ip',
                                  `created_at` int(11) NOT NULL COMMENT '登录时间',
                                  PRIMARY KEY (`id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员登录日志表';");
            DB::statement("CREATE TABLE `error_log` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `message` text NOT NULL COMMENT '错误信息',
                                  `file` varchar(255) NOT NULL COMMENT '错误文件',
                                  `line` int(11) NOT NULL COMMENT '错误代码所在具体行数',
                                  `ip` varchar(255) NOT NULL COMMENT '访问ip',
                                  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '访问url',
                                  `method` varchar(255) NOT NULL COMMENT '访问方法',
                                  `param` text NOT NULL COMMENT '访问参数',
                                  `header` text NOT NULL COMMENT 'header',
                                  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0未处理，1已处理',
                                  `updated_at` int(11) NOT NULL,
                                  `created_at` int(11) NOT NULL,
                                  PRIMARY KEY (`id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='错误信息记录表';");
            DB::statement("CREATE TABLE `goods` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `name` varchar(50) NOT NULL COMMENT '商品名称',
                                  `number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '库存',
                                  PRIMARY KEY (`id`),
                                  UNIQUE KEY `NAME` (`name`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品表';");
            DB::statement("CREATE TABLE `goods_order` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `user_id` int(11) NOT NULL COMMENT '用户id',
                                  `goods_id` int(11) NOT NULL COMMENT '商品id',
                                  `goods_number` int(10) unsigned DEFAULT NULL COMMENT '数量',
                                  PRIMARY KEY (`id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品订单表';");
            DB::statement("CREATE TABLE `menu` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父id',
                                  `name` varchar(255) NOT NULL COMMENT '名称',
                                  `alias` varchar(255) DEFAULT NULL COMMENT '别名',
                                  `icon` varchar(255) DEFAULT NULL COMMENT '菜单图标',
                                  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序，按照降序',
                                  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0禁用 1启用',
                                  `updated_at` int(11) DEFAULT '0',
                                  `created_at` int(11) NOT NULL,
                                  PRIMARY KEY (`id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单表';");
            DB::statement("CREATE TABLE `permission` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
                                  `menu_id` int(11) NOT NULL DEFAULT '0' COMMENT '菜单id',
                                  `created_at` int(11) NOT NULL,
                                  PRIMARY KEY (`id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色权限表';");
            DB::statement("CREATE TABLE `role` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `name` varchar(255) NOT NULL COMMENT '名称',
                                  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0禁用 1启用',
                                  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是超级管理员：0否 1是',
                                  `created_by` int(11) NOT NULL COMMENT '创建人id',
                                  `updated_at` int(11) DEFAULT '0',
                                  `created_at` int(11) NOT NULL,
                                  PRIMARY KEY (`id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员角色表';");
            DB::statement("CREATE TABLE `sms_notify` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `value` varchar(255) NOT NULL COMMENT '内容',
                                  `status` tinyint(1) NOT NULL COMMENT '状态：0未发送 1已发送',
                                  `updated_at` int(11) NOT NULL,
                                  `created_at` int(11) NOT NULL,
                                  PRIMARY KEY (`id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短信通知表';");
            $this->info('数据表创建成功');
            return true;
        } catch (\Exception $e) {
            v($e->getMessage());
        }
    }
}
