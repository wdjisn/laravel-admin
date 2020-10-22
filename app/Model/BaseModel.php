<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/18
 * Time: 17:20
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    protected $primaryKey = 'id';
    public $timestamps = true;

    /**
     * 获取当前时间
     * @return int
     */
    public function freshTimestamp()
    {
        return time();
    }

    /**
     * 避免转换时间戳为时间字符串
     * @param mixed $value
     * @return mixed|string|null
     */
    public function fromDateTime($value)
    {
        return $value;
    }
}
