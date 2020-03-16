<?php


namespace app\api\validate;


use app\api\model\BaseModel;

class IDCollection extends BaseValidate
{
    //dis运用规则
    protected $rule = [
        'ids' => 'require|checkIDs'             //require 表示参数必须要传入
    ];

    //定义dis检测不通过后的一个信息提示
    protected $message = [
        'ids' => 'ids参数必须是以逗号分隔的多个正整数'
    ];


    //value代表客户端传过来id1,id2,...(逗号分隔的字符串)
    protected function checkIDs($value){
        //字符串分隔成数组
        $values = explode(',', $value);
        //如果是空的，就返回false.
        if(empty($values)){
            return false;
        }
        //遍历每个id是否是正整数
        foreach ($values as $id){
            //写的时候注意！！！！！，到底要不要感叹号。
            if(!$this->isPositiveInteger($id)){
                return false;
            }
        }
        return true;
    }

}