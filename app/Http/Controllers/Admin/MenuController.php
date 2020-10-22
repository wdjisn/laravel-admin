<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/16
 * Time: 18:10
 */

namespace App\Http\Controllers\Admin;

use App\Model\Menu;
use App\Service\MenuService;

class MenuController extends BaseController
{

    /**
     * 获取菜单列表
     */
    public function getMenuList()
    {
        $where['parent_id'] = $this->requestArr['parent_id'] ?? null;

        $data   = Menu::getMenus($where);
        $result = getTree($data);
        $list   = getList($result);
        foreach ($list as &$val) {
            $val['status'] = $val['status'] == 1 ? true : false;
        }

        successReturn($list);
    }

    /**
     * 获取树形菜单
     */
    public function getMenuTree()
    {
        $data = Menu::getMenus([]);
        $result = getTree($data);

        successReturn($result);
    }

    /**
     * 获取菜单详情
     */
    public function getMenuInfo()
    {
        $id = $this->requestArr['id'];

        $info = MenuService::getInfoById($id);

        successReturn($info);
    }

    /**
     * 添加菜单
     */
    public function createMenu()
    {
        $data['alias']     = $this->requestArr['alias'] ?? '';
        $data['name']      = $this->requestArr['name'];
        $data['icon']      = $this->requestArr['icon'] ?? '';
        $data['sort']      = $this->requestArr['sort'] ?? 0;
        $data['parent_id'] = $this->requestArr['parent_id'] ?? 0;
        $data['status']    = $this->requestArr['status'] == 1 ? 1 : 0;

        $result = MenuService::addMenu($data);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }
        successReturn();
    }

    /**
     * 编辑菜单
     */
    public function editMenu()
    {
        $id                = $this->requestArr['id'];
        $data['alias']     = $this->requestArr['alias'] ?? '';
        $data['name']      = $this->requestArr['name'];
        $data['icon']      = $this->requestArr['icon'] ?? '';
        $data['sort']      = $this->requestArr['sort'] ?? 0;
        $data['parent_id'] = $this->requestArr['parent_id'] ?? 0;
        $data['status']    = $this->requestArr['status'] == 1 ? 1 : 0;

        $result = MenuService::editMenu($id,$data);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }
        successReturn();
    }

    /**
     * 快捷修改菜单
     */
    public function quickEditMenu()
    {
        $id = $this->requestArr['id'];
        $data['status'] = $this->requestArr['status'] == 1 ? 1 : 0;

        $result = Menu::editMenu($id,$data);
        if ($result) {
            successReturn();
        }
        errorReturn($result['msg']);
    }

    /**
     * 删除菜单
     */
    public function deleteMenu()
    {
        $id = $this->requestArr['id'];

        $result = MenuService::delMenuById($id);
        if (!$result['status']) {
            errorReturn($result['msg']);
        }
        successReturn();
    }
}
