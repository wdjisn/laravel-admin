<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/19
 * Time: 17:53
 */

namespace App\Model;

class Menu extends BaseModel
{

    protected $table = 'menu';

    /**
     * 获取菜单列表
     * @param $param
     * @return mixed
     */
    public static function getMenus($param)
    {
        $where = Array();
        if (array_key_exists('status',$param) && $param['status'] != null) {
            $where[] = ['status','=',(int)$param['status']];
        }
        if (array_key_exists('parent_id',$param) && $param['parent_id'] != null) {
            $where[] = ['parent_id','=',(int)$param['parent_id']];
        }

        $list = self::where($where)->orderBy('sort')->get()->toArray();

        return $list;
    }

    /**
     * 获取菜单信息
     * @param $param
     * @return array
     */
    public static function getInfo($param)
    {
        $where = Array();
        if (array_key_exists('id',$param) && !empty($param['id'])) {
            $where[] = ['id','=',$param['id']];
        }

        $info  = self::where($where)->first();
        if ($info) {
            return $info->toArray();
        }
        return [];
    }

    /**
     * 添加菜单
     * @param $info
     * @return mixed
     */
    public static function addMenu($info)
    {
        $time = time();
        $info['updated_at'] = $time;
        $info['created_at'] = $time;

        return self::insertGetId($info);
    }

    /**
     * 编辑菜单
     * @param $id
     * @param $info
     * @return mixed
     */
    public static function editMenu($id, $info)
    {
        $info['updated_at'] = time();

        return self::where(['id' => $id])->update($info);
    }

    /**
     * 获取菜单数量
     * @param array $param
     * @return mixed
     */
    public static function getCount($param = Array())
    {
        $where = Array();
        if (array_key_exists('parent_id',$param)) {
            $where[] = ['parent_id','=',$param['parent_id']];
        }

        return self::where($where)->count();
    }

    /**
     * 删除菜单
     * @param $id
     * @return mixed
     */
    public static function delMenu($id)
    {
        return self::where(['id' => $id])->delete();
    }
}
