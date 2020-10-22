<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/18
 * Time: 11:41
 */

/**
 * 调试打印
 * @param $msg
 */
function v($msg)
{
    echo '<pre>';
    var_dump($msg);
    exit;
}

/**
 * @param array $data
 * @param string $msg
 */
function successReturn($data = Array(), $msg = '请求成功')
{
    $result['code'] = 200;
    $result['msg']  = $msg;
    $result['data'] = $data;
    echo json_encode($result);
    exit();
}

/**
 * @param string $msg
 * @param int $code
 * @param array $data
 */
function errorReturn($msg = '请求失败', $code = 1000, $data = Array())
{
    $result['code'] = $code;
    $result['msg']  = $msg;
    $result['data'] = $data;
    echo json_encode($result);
    exit();
}

/**
 * @param array $data
 * @param string $msg
 * @return mixed
 */
function successMsg($data = Array(),$msg = '请求成功')
{
    $result['msg'] = $msg;
    $result['data'] = $data;
    $result['status'] = true;
    return $result;
}

/**
 * @param string $msg
 * @param array $data
 * @return mixed
 */
function errorMsg($msg = '参数错误',$data = Array())
{
    $result['msg'] = $msg;
    $result['data'] = $data;
    $result['status'] = false;
    return $result;
}

/**
 * 随机生成字符串
 * @param int $length
 * @return string
 */
function randString($length = 4)
{
    # 字符集
    $chars = [
        'S', '6', 'U', 'V', 'W', '5', 'X', 'Y', 'Z', '0', '1', '2',
        'l', 'm', 'n', 'o', 'p', '7', 'q', 'r', 's', 't', 'u', 'v',
        'w', 'x', 'y', 'z', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
        '3', 'a', 'b', 'c', 'd', '4', 'e', 'N', 'O', 'P', 'f', 'T',
        'g', 'h', 'i', 'j', 'k', 'H', 'I', 'J', '8', 'K', 'L', 'M',
        'Q', 'R',
    ];

    $keys = array_rand($chars, $length);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $chars[$keys[$i]];
    }
    return $string;
}

/**
 * 获取菜单资源的树状结构
 * @param $list
 * @param int $pid
 * @return null
 */
function getTree($list, $pid = 0)
{
    $data = Array();
    if (is_array($list)) {
        foreach ($list as $val) {
            if ($val['parent_id'] == $pid) {
                unset($val['updated_at']);
                $children = getTree($list,$val['id']);
                if (count($children)) {
                    $val['children'] = $children;
                }
                $data[] = $val;
            }
        }
    }
    return $data;
}

/**
 * @param $data
 * @param int $level
 * @return array
 */
function getList($data, $level = 1)
{
    $list = Array();
    foreach ($data as $key=>$val) {
        $tmp = $val;
        $tmp['level'] = $level;
        unset($tmp['children']);
        $list[] = $tmp;
        if (array_key_exists('children',$val) && is_array($val['children'])) {
            $res  = getList($val['children'],$level+1);
            $list = array_merge($list,$res);
        }
    }

    return $list;
}

/**
 * 获取文件行数
 * @param $file
 * @return int
 */
function getFileRows($file)
{
    set_time_limit(0);
    $line = 0 ;
    try {
        $handle = fopen($file , 'r');
        if ($handle) {
            # 获取文件的一行内容，注意：需要php5才支持该函数；
            while(stream_get_line($handle, 40960, "\r\n")){
                $line++;
            }
            # 关闭文件
            fclose($handle);
        }
        return $line;
    }catch (\Exception $e) {
        return 0;
    }
}

/**
 * 获取文件指定行内容
 * @param $file
 * @param $line
 * @param int $length
 * @return bool|string|null
 */
function getLine($file, $line, $length = 40960)
{
    $i = 1;  # 行数
    $returnTxt = null;  # 初始化返回
    $handle = @fopen($file, "r");
    if ($handle) {
        while (!feof($handle)) {
            $buffer = fgets($handle, $length);
            if ($line == $i) $returnTxt = $buffer;
            $i++;
        }
        fclose($handle);
    }
    return $returnTxt;
}
