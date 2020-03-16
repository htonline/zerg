<?php


namespace app\lib\exception;
//Banner没有找到

class BannerMissException extends BaseException
{
    public $code = 404;
    public $msg = '请求的Banner不存在';
    public $errorCode = 40000;//这个就随意了，要有自己的错误编码体系

}