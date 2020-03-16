<?php
//extra 目录下的文件都会被TP5框架默认加载
//自定义配置文件
//非保密信息配置文件

return[
    //它的取值+图片相对路径=可以取到图片的完整url路径
    'img_prefix' => 'http://z.cn/images',
    //令牌过期时间
    'token_expire_in' => 7200
];