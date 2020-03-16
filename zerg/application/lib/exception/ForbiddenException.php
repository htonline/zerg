<?php


namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    //禁止访问异常
    public $code = 403;
    public $msg = '权限不够';
    public $errorCode = 10001;

}