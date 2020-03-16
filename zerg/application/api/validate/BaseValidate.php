<?php


namespace app\api\validate;


use app\lib\exception\ParameterException;
use think\Exception;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck()
    {
        //获取http传入的参数
        //对这些参数进行校验
        $request = Request::instance();
        $params = $request->param();//获取所有参数

        //因为我们在Validate类内，所以不需要再new一个，用this
        //用result记录验证结果
        $result = $this->batch()->check($params);
        if(!$result){
            $e = new ParameterException([
                'msg' => $this->error
            ]);//成员变量初始化操作
            throw $e;

        }
        else{
            return true;
        }
    }

    //自定义规则
    protected function isPositiveInteger($value, $rule = '', $data = '', $field = '')//自定义验证规则
    {
        if(is_numeric($value) && is_int($value + 0) && ($value + 0) > 0){
            return true;
        }
        else{
            return false;
        }
    }

    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if($result){
            return true;
        }
        else{
            return false;
        }
    }

    protected function isNotEmpty($value, $rule = '', $data = '', $field = '')
    {
        if(empty($value)){
            return false;
        }
        else{
            return true;
        }
    }

    //接收从客户端传过来的所有参数，根据规则特定筛选
    public function getDataByRule($arrays)
    {
        if(array_key_exists('user_id', $arrays) | array_key_exists('uid', $arrays)){
            //不允许包含user_id或者uid，防止恶意覆盖user_id外键
            throw new ParameterException([
                'msg' => '参数中包含有非法参数名user_id或者uid'
            ]);
        }
        //保存6个变量具体的取值（'name','mobile','province','city','country','detail'）
        $newArray = [];
        //遍历rule数组，赋值
        foreach ($this->rule as $key => $value ) {
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }

}