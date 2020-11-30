<?php
return [
    # app
    'app' => [
        'name'        => 'LaravelAdmin',
        'environment' => false,
        'jwt_secret'  => 'X0p7tv5TFL2SyADG3bPW9defThiI8KQR'
    ],
    # MongoDB
    'mongo' => [
        'host'     => '',
        'port'     => '',
        'username' => '',
        'password' => '',
        'database' => ''
    ],
    # 短信
    'sms' => [
        'sign'   => '',
        'key'    => '',
        'secret' => ''
    ],
    # 七牛云
    'qiniu' => [
        'bucket'  => '',
        'key'     => '',
        'secret'  => '',
        'storage' => ''
    ],
    # 阿里云OSS
    'oss' => [
        'key'      => '',
        'secret'   => '',
        'bucket'   => '',
        'endpoint' => '',
        'gateway'  => ''
    ],
    # 钉钉
    'ding' => [
        'token' => ''
    ],
    # 微信
    'weixin' => [
        'id'     => '',
        'secret' => ''
    ]
];
