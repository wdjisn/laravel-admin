<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/6
 * Time: 18:20
 */

namespace App\Service;

use App\Model\Menu;
use App\Model\Permission;
use Illuminate\Support\Facades\DB;

class MenuService
{

    /**
     * 根据Id获取菜单信息
     * @param $id
     * @return array|bool
     */
    public static function getInfoById($id)
    {
        if ($id <= 0) {
            return [];
        }
        return Menu::getInfo(['id' => $id]);
    }

    /**
     * 添加菜单
     * @param $param
     * @return mixed
     */
    public static function addMenu($param)
    {
        # 参数验证
        if (!$param['name']) {
            errorReturn('请输入菜单名称');
        }
        if (!$param['alias']) {
            return errorMsg('请输入菜单别名');
        }
        if ($param['parent_id'] == 0 && !$param['icon']) {
            return errorMsg('请输入菜单图标类名');
        }
        if ($param['sort'] < 0) {
            errorReturn('请输入正确的排序值');
        }

        $menuId = Menu::addMenu($param);
        if ($menuId) {
            return successMsg(['id' => $menuId]);
        }
        return errorMsg('操作失败');
    }

    /**
     * 编辑菜单
     * @param $id
     * @param $param
     * @return mixed
     */
    public static function editMenu($id, $param)
    {
        # 参数验证
        if (!$param['name']) {
            errorReturn('请输入菜单名称');
        }
        if (!$param['alias']) {
            return errorMsg('请输入菜单别名');
        }
        if ($param['parent_id'] == 0 && !$param['icon']) {
            return errorMsg('请输入菜单图标类名');
        }
        if ($param['sort'] < 0) {
            errorReturn('请输入正确的排序值');
        }

        $result = Menu::editMenu($id,$param);
        if ($result) {
            return successMsg();
        }
        return errorMsg('操作失败');
    }

    /**
     * 删除菜单
     * @param $id
     * @return mixed
     */
    public static function delMenuById($id)
    {
        # 判断是否有子菜单
        $count = Menu::getCount(['parent_id' => $id]);
        if ($count) {
            return errorMsg('此菜单有子菜单，无法删除');
        }

        DB::beginTransaction();
        try {
            Menu::delMenu($id);
            Permission::delPermissionByMenu($id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return errorMsg('操作失败，请稍后重试');
        }
        return successMsg();
    }
}
