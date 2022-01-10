## 项目简介
LaravelAdmin是基于PHP开发的基础管理后台系统，做到开箱即用，为新项目开发省去了基础功能开发的步骤；此系统采用前后端分离模式，后端使用Laravel，前端使用vue；主要包含：登录、注销、可视化数据大屏、管理员、角色管理、菜单管理、权限管理、错误日志、登录日志、访问日志、获取服务器CPU使用率、内存使用率等功能。后端主要使用Artisan命令行、Jobs消息队列、 Rules验证规则、Restful API、Composer扩展包、Redis秒杀、Extend自定义扩展类：微信授权、钉钉告警推送、MongoDB、阿里云OSS、七牛云存储、七牛云直播、php-jwt TOKEN、Phpoffice、MySql数据库字典等技术。


## 项目截图
##### 数据大屏
![](https://sobj.oss-cn-beijing.aliyuncs.com/image/20201022/dataV.png)
##### 菜单管理
![](https://sobj.oss-cn-beijing.aliyuncs.com/image/20201130/menu.png)
##### 权限管理
![](https://sobj.oss-cn-beijing.aliyuncs.com/image/20201130/permission.png)
##### 错误日志
![](https://sobj.oss-cn-beijing.aliyuncs.com/image/20201130/error.png)


## 后端安装步骤
- git clone https://github.com/wdjisn/laravel-admin.git
- composer update
- 复制 .env.example 为 .env
- 修改 .env 文件中mysql、redis的配置
- 执行 php artisan key:generate 命令，生成 APP_KEY
- 数据表初始化 php artisan createTable
- 管理员初始化 php artisan adminInitialize admin（账号） --password=123456（密码）


## 后端目录简介
```
├── app 
│   ├── Console
│   ├── ├── Commands 命令行
│   ├── ├── ├── CreateTable.php 初始化数据表
│   ├── ├── ├── AdminInitialize.php 初始化超管员
│   ├── Exceptions
│   ├── ├── Handler.php 捕获异常并推送至钉钉
│   ├── Extend 自定义扩展类
│   ├── Helpers
│   ├── ├── function.php 自定义常用函数
│   ├── Http 控制器层
│   ├── Jobs 消息队列
│   ├── Model 模型层
│   ├── Rules 验证规则
│   ├── Service 服务层
├── config
│   ├── style.php 自定义配置文件
│   ├── database.php 数据库配置文件
├── docs 自定义文档
├── routes
│   ├── admin.php 管理后台路由
├── .env 配置文件
├── composer.json
├── README.md
```


## 前端仓库地址
- https://github.com/wdjisn/vue-admin.git


## 疑问解答
- 微信：wdjisn
