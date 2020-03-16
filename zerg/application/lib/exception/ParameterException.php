<?php


namespace app\lib\exception;
//参数异常错误

class ParameterException extends BaseException
{
    //默认初始值
    public $code = 400;
    public $msg = '参数错误';
    public $errorCode = 10000;

}