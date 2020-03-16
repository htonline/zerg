<?php


namespace app\api\controller;


use app\api\service\Token as TokenService;
use think\Controller;

class BaseController extends Controller
{
    //管理员和用户都可以访问
    protected function checkPrimaryScope()
    {
        TokenService::needPrimaryScope();
    }

    //只有用户才能访问的接口权限
    protected function checkExclusiveScope()
    {
        TokenService::needExclusiveScope();
    }

}