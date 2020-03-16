<?php


namespace app\api\validate;


use think\Validate;

//继承Validate
class TestValidate extends Validate
{
    //protected:受保护的；可见性
    //rule 是固定变量名
    protected $rule = [
        'name'=>'require|max:10',
        'email'=>'email'
    ];
    //验证器定义完毕
}