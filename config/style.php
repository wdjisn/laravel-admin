<?php
return [
    # app
    'app' => [
        'name'        => 'LaravelAdmin',
        'environment' => false,
        'secret'      => 'Sl7qkF2DKglAdlk4397qdKCUf3',
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
    # 七牛云直播
    'qnPili' => [
        'ak'        => '',
        'sk'        => '',
        'hubName'   => '',
        'streamKey' => '',
        'rtmpPush'  => '',
        'rtmpLive'  => '',
        'hlsLive'   => '',
        'hdlLive'   => ''
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
