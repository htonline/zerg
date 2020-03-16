<?php


namespace app\api\validate;
//id正整数检验

use think\Validate;

class IDMustBePostiveInt extends BaseValidate
{
    //设置规则
    protected $rule = [
        'id' => 'require|isPositiveInteger',
    ];

    //定义dis检测不通过后的一个信息提示
    protected $message = [
        'id' => 'id必须是正整数'
    ];

}