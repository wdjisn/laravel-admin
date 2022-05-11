<?php

namespace App\Extend;

use Elasticsearch\ClientBuilder;

/**
 * php Elasticsearch 使用示列类
 */
class Elasticsearch
{

    public $es;

    /**
     * 构造方法
     * Elasticsearch constructor.
     */
    public function __construct()
    {
        // host数组可配置多个节点
        $params   = ['127.0.0.1:9200'];
        $this->es = ClientBuilder::create()
                  ->setHosts($params)
                  ->build();
    }

    /**
     * 创建索引（指定模板）
     *
     * 几个关键属性
     *
     * String类，分为两种：
     * text：可分词，生成索引，不参与聚合
     * keyword：不可分词，数据会作为完整字段进行匹配，可参与聚合
     *
     * Numberical数值类型，分两类：
     * 基本数据类型：long、integer、short、byte、double、float、half_float
     * 浮点数高精度类型：scaled_float（需要制定精度因子，10或100这样，es会把真实值与之相乘后存储，取出时还原）
     *
     * Date日期类型
     * ES 可以对日期格式，化为字符串存储
     *
     * ik_max_word 和 ik_smart
     * ik_max_word：会对文本做最细力度的拆分
     * ik_smart：会对文本做最粗粒度的拆分
     * 两种分词器的最佳实践： 索引时用ik_max_word（面面俱到）， 搜索时用ik_smart（精准匹配）。
     *
     * 分词器 analyzer 和 search_analyzer
     * 分词器 analyzer 的作用有二：
     * 一：插入文档时，将 text 类型字段做分词，然后插入 倒排索引。
     * 二：在查询时，先对 text  类型输入做分词， 再去倒排索引搜索。
     *
     * 如果想要"索引"和"查询"，使用不同的分词器，那么只需要在字段上使用search_analyzer。这样，索引只看analyzer，查询就看 search_analyzer。
     */
    public function createIndex()
    {
        $params = [
            'index' => 'test',                          // 索引名称
            'body'  => [
                'settings' => [                         // 配置
                    'number_of_shards'   => 3,          // 主分片数
                    'number_of_replicas' => 1           // 主分片的副本数
                ],
                'mappings'=> [                          // 映射
                    '_source' => [                      // 存储原始文档
                        'enabled' => 'true'
                    ],
                    'properties' => [
                        'id' => [
                            'type' => 'long'
                        ],
                        'author' => [
                            'type' => 'keyword'
                        ],
                        'age' => [
                            'type' => 'integer'
                        ],
                        'content' => [
                            'type'  => 'text',
                            'index' => 'true',
                            'analyzer' => 'ik_max_word'
                        ],
                        'create_at' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                        ]
                    ]
                ]
            ]
        ];
        $response = $this->es->indices()->create($params);
        v($response);
    }

    /**
     * 删除索引
     */
    public function deleteIndex()
    {
        $params = ['index' => 'test'];
        $response = $this->es->indices()->delete($params);
        v($response);
    }

    /**
     * 修改索引配置参数
     */
    public function updateIndexSetting()
    {
        $params = [
            'index' => 'test',                 // 索引名
            'body' => [
                'settings' => [                // 修改设置
                    'number_of_replicas' => 5  // 副本数
                ]
            ]
        ];
        $response = $this->es->indices()->putSettings($params);
        v($response);
    }

    /**
     * 获取一个或多个索引的当前配置参数
     */
    public function getSettings()
    {
        $params = ['index' => 'test'];
        $response = $this->es->indices()->getSettings($params);
        v($response);
    }

    /**
     * 查询一个或多个索引的mapping定义
     */
    public function getMapping()
    {
        // 询所有的mapping定义
        /*$response = $this->es->indices()->getMapping();*/


        // 查询my_index和my_index2两个索引的mapping定义
        /*$params = ['index' => [ 'my_index', 'my_index2']];
        $response = $this->es->indices()->getMapping($params);*/


        // 查询指定索引的Mapping定义
        $params = ['index' => 'test'];
        $response = $this->es->indices()->getMapping($params);
        v($response);
    }

    /**
     * 创建文档
     */
    public function indexDoc()
    {
        $body = [
            ['id' => 1, 'author' => 'wdjisn', 'age' => 28, 'content' => '只有承担起旅途风雨，才能最终守得住彩虹满天。', 'create_at' => '2022-05-01 12:35:46'],
            ['id' => 2, 'author' => 'style', 'age' => 30, 'content' => '因为四季轮回，我们感受着自然变幻，体味着春华秋实。', 'create_at' => '2022-05-06 09:41:32'],
            ['id' => 3, 'author' => 'wdjisn', 'age' => 28, 'content' => '环境永远不会十全十美，消极的人受环境控制，积极的人却控制环境。', 'create_at' => '2022-05-05 18:24:31'],
            ['id' => 4, 'author' => 'shuishou', 'age' => 35, 'content' => '走得最慢的人，只要他不丧失目标，也比漫无目的地徘徊的人走得快。', 'create_at' => '2022-05-08 10:01:53'],
            ['id' => 5, 'author' => 'dali', 'age' => 32, 'content' => '一个人一定要有自己好好过日子的信心，要有别人无法忽视的能力，这是幸福的基本。', 'create_at' => '2022-05-10 20:30:01']
        ];

        foreach ($body as $key=>$value) {
            $params = [
                'index' => 'test',
                'type'  => '_doc',
                'id'    => $value['id'],  // 设置文档Id, 可以忽略Id, Es也会自动生成
                'body'  => $value
            ];
            $response = $this->es->index($params);
        }
    }

    /**
     * 查询文档
     */
    public function getDoc()
    {
        $params = [
            'index' => 'test',
            'type'  => '_doc',
            'id'    => 1
        ];
        $response = $this->es->get($params);
        v($response);
    }

    /**
     * 搜索文档
     */
    public function searchDoc()
    {
        $params = [
            'index' => 'test',
            'type'  => '_doc',
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            ['match' => [
                                    'content' => '人'
                                ]
                            ],
                            ['match' => [
                                'author' => 'wdjisn'
                                ]
                            ]
                        ],
                    ],
                ],
                'sort' => ['create_at' => ['order' => 'desc']], // 排序
                'from' => 0,  // 分页
                'size' => 10  // 分页
            ]
        ];
        $response = $this->es->search($params);
        v($response);
    }

    /**
     * 更新文档
     */
    public function updateDoc()
    {
        $params = [
            'index' => 'test',
            'type'  => '_doc',
            'id'    => 1,
            'body'  => [
                'doc' => [  // doc包含的内容就是我们想更新的字段内容
                    'content' => '行动是成功的阶梯，行动越多，登得越高。'
                ]
            ]
        ];
        $response = $this->es->update($params);
        v($response);
    }

    /**
     * 删除文档
     */
    public function deleteDoc()
    {
        $params = [
            'index' => 'test',
            'type'  => '_dco',
            'id'    => 1
        ];
        $response = $this->es->delete($params);
        v($response);
    }
}
