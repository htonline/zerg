<?php


namespace app\api\controller\v1;


use app\api\controller\BaseController;

class Pay extends BaseController
{
    //只有用户可以访问
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];

    //请求预订单信息(API去微信服务器生成的一个微信服务器所要求的订单，不是自己管理的订单)
    public function getPreOrder()
    {

    }

}