<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/29
 * Time: 12:07
 */

namespace App\Extend;

class MongoDB
{

    private $host;                  # IP
    private $port;                  # 端口号
    private $username;              # 用户名
    private $password;              # 密码
    private $database;              # 连接的数据库
    private $client;                # 保存MongoDB连接对象
    private static $instance;       # 保存实例（在函数执行完后，静态变量值仍然保存）

    /**
     * 不允许初始化
     * LocationGps constructor.
     */
    private function __construct()
    {
        $this->initConfig();
        $this->initConnect();
    }

    /**
     * 不允许被克隆
     */
    private function __clone() {}

    /**
     * 获取单例
     * @return MongoDB
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 初始化连接配置参数
     */
    private function initConfig()
    {
        $this->host = config('style.mongo.host');
        $this->port = config('style.mongo.port');
        $this->username = config('style.mongo.username');
        $this->password = config('style.mongo.password');
        $this->database = config('style.mongo.database');
    }

    /**
     * 连接数据库
     */
    private function initConnect()
    {
        $auth = 'mongodb://'.$this->username.':'.$this->password.'@'.$this->host.':'.$this->port.'/'.$this->database;
        $this->client = new \MongoDB\Driver\Manager($auth);
    }

    /**
     * 查询
     * @param $filter
     * @param $options
     * @param $collection
     * @return mixed
     */
    private static function mongoQuery($filter, $options, $collection)
    {
        $query  = new \MongoDB\Driver\Query($filter,$options);
        $cursor = self::getInstance()->client->executeQuery(self::getInstance()->database.'.'.$collection, $query)->toArray();

        return $cursor;
    }

    /**
     * 测试方法（获取某一设备的轨迹）
     * 调用方法： MongoDB::getTracksBySn($sn,$start,$end)
     * @param $sn
     * @param $start
     * @param $end
     * @return array
     */
    public static function getTracksBySn($sn, $start, $end)
    {
        $data = Array();
        if (!$sn || !$start || !$end) {
            return $data;
        }

        # 集合名
        $collection = 'location';
        # 筛选条件
        $filter = ['sn' => $sn,'location_time' => ['$gte' => (int)$start,'$lte' => (int)$end]];
        # 查询选项
        $options = ['projection' => ['_id' => false,'sn' => true,'lat' => true,'lng' => true,'location_time' => true],  # 查询字段
                    'sort' => ['location_time' => -1],                                                                  # 排序规则
                    // 'limit' => 1                                                                                     # 限制条数
        ];
        # 查询
        $data = self::mongoQuery($filter,$options,$collection);

        return $data;
    }

    /**
     * 测试方法（获取多个设备最后一条定位信息）
     * 调用方法： MongoDB::getLocationBySns($sns)
     * @param $sns
     * @return array
     */
    public static function getLocationBySns($sns)
    {
        $data = Array();
        if (!count($sns)) {
            return $data;
        }

        # 集合名
        $collection = 'location';
        $document = [
            'aggregate' => $collection,
            'pipeline'  => [
                [
                    '$match' => [    # 筛选条件
                        'number' => ['$in' => $sns]
                    ]
                ],
                [
                    '$project' => [     # 查询字段
                        'number'        => '$number',
                        'lat'           => '$lat',
                        'lng'           => '$lng',
                        'height'        => '$height',
                        'speed'         => '$speed',
                        'direction'     => '$direction',
                        'location_time' => '$location_time'
                    ]
                ],
                [
                    '$group' => [
                        '_id'           => '$number',   # group字段
                        'number'        => ['$first' => '$number'],       # 对应查询字段
                        'lat'           => ['$first' => '$lat'],          # 对应查询字段
                        'lng'           => ['$first' => '$lng'],          # 对应查询字段
                        'height'        => ['$first' => '$height'],       # 对应查询字段
                        'speed'         => ['$first' => '$speed'],        # 对应查询字段
                        'direction'     => ['$first' => '$direction'],    # 对应查询字段
                        'location_time' => ['$first' => '$location_time'],# 对应查询字段
                        'count'         => ['$sum' => 1]
                    ]
                ],
                [
                    '$sort' => [    # 排序规则
                        'location_time' => -1
                    ]
                ]
            ],
            'cursor' => ['batchSize' => 1]
        ];
        $command = new \MongoDB\Driver\Command($document);
        $cursor  = self::getInstance()->client->executeCommand(self::getInstance()->database, $command)->toArray();

        return $cursor;
    }
}
