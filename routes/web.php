<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // 微信token认证
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce     = $_GET["nonce"];
    $echostr   = $_GET["echostr"];

    // 你在微信公众号后台的设置的Token
    $token = "R3QGOqgF4LAje9M7HJAR1w1LM9kJr4wT";

    // 1）将token、timestamp、nonce三个参数进行字典序排序
    $tmpArr = array($nonce, $token, $timestamp);
    sort($tmpArr, SORT_STRING);

    // 2）将三个参数字符串拼接成一个字符串进行sha1加密
    $str  = implode($tmpArr);
    $sign = sha1($str);

    // 3）开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
    if ($sign == $signature) {
        echo $echostr;
    }
    // return null;
});

Route::get("/ddoc","Controller@getDdoc");                                    // 获取数据字典
Route::get("/image/{file_path}/{file_name}","Controller@imageBrowse");       // storage文件夹下图片预览
Route::get("/video/{file_path}/{file_name}","Controller@videoBrowse");       // storage文件夹下视频预览

# 引入路由文件
include __DIR__ . '/admin.php';
include __DIR__ . '/api.php';
