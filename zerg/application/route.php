<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');

Route::get('api/:version/theme','api/:version.Theme/getSimpleList');

Route::get('api/:version/theme/:id','api/:version.Theme/getComplexOne');
//    (/:id)可以理解为获取某个theme的详细信息,为了与上面那个区分

Route::get('api/:version/product/recent','api/:version.Product/getRecent');
Route::get('api/:version/product/by_category','api/:version.Product/getAllInCategory');
Route::get('api/:version/product/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
////路由规则，顺序匹配，如果直接把recent放到id下面，再调用recent会报错！
////所以必须限定id是正整数的时候，才匹配这个接口。
////第三个参数不管，第四个是对参数的一个限定（变量规则，接收数组），用正则表达式限定
//路由分组：
//Route::group('api/:version/product',function (){
//    Route::get('/recent','api/:version.Product/getRecent');
//    Route::get('/by_category','api/:version.Product/getAllInCategory');
//    Route::get('/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
//});

Route::get('api/:version/category/all','api/:version.Category/getAllCategories');

Route::post('api/:version/token/user','api/:version.Token/getToken');
//安全性稍微提高一点，隐藏到bod里去了，https最好
Route::post('api/:version/token/verify','api/:version.Token/verifyToken');

Route::post('api/:version/address','api/:version.Address/createOrUpdateAddress');
Route::get('api/:version/address','api/:version.Address/getUserAddress');

Route::post('api/:version/order','api/:version.Order/placeOrder');
Route::post('api/:version/order/by_user','api/:version.Order/getSummaryByUser');
Route::post('api/:version/order/:id','api/:version.Order/getDetail',[],['id'=>'\d+']);

