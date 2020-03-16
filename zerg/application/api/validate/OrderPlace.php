<?php


namespace app\api\validate;


use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidate
{
//    products是一个二维数组，包含商品id和购买数量
//    protected $products = [
//        [
//            'product_id' => 1,
//            'count' => 3
//        ],
//        [
//            'product_id' => 2,
//            'count' => 3
//        ],
//        [
//            'product_id' => 3,
//            'count' => 3
//        ]
//    ];

    //验证数组
    protected $rule = [
        'products' => 'checkProducts'
    ];
    //验证数组下的子项
    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger'
    ];

    //自定义检测上面那个二维数组的方法，因为毕竟特殊，就不写在BaseValidate
    protected function checkProducts($values){
        //1.参数必须是数组
        if(!is_array($values)){
            throw new ParameterException([
                'msg' => '参数不正确'
            ]);
        }

        //2.参数不能为空
        if(empty($values)){
            throw new ParameterException([
                'msg' => '商品列标不能为空'
            ]);
        }

        //循环遍历每个子元素，$value现在就对应着product_id和count
        foreach ($values as $value)
        {
            $this->checkProduct($value);
        }
        return true;
    }

    //验证子项里的product_id和count
    protected function checkProduct($value)
    {
        //一个validate验证器下面只有特定的rule是可以被自动验证的
        //singleRule只能在这里被手动调用。
        $validate = new BaseValidate($this->singleRule);//验证器最基本最直接的用法
        $result = $validate->check($value); //调用validate的check方法验证规则
        if(!$result){
            throw new ParameterException([
                'msg' => '商品列标参数错误'
            ]);
        }
    }


}