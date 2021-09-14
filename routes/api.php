<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->namespace('Api')->group(function () {
    Route::post('/send/code', 'IndexController@sendCode');                      # 发送短信验证码
    Route::post('/upload/image/base64', 'IndexController@uploadImageBase64');   # base64字符串上传图片
    Route::post('/create/seckill','IndexController@createSeckill');             # 创建秒杀
    Route::post('/user/seckill','IndexController@userSeckill');                 # 用户秒杀
    Route::get('/seckill/result','IndexController@seckillResult');              # 显示秒杀结果
    Route::post('/create/live','IndexController@createLive');                   # 创建直播流
    Route::get('/lives','IndexController@getLiveList');                         # 获取直播列表
});
