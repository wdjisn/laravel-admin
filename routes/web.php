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
    return null;
});

Route::get("/image/{file_path}/{file_name}","Controller@imageBrowse");       // storage文件夹下图片预览
Route::get("/video/{file_path}/{file_name}","Controller@videoBrowse");       // storage文件夹下视频预览

# 引入admin.php路由
include __DIR__ . '/admin.php';
