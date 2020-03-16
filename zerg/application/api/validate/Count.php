<?php


namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule = [
        'count' => 'isPositiveInteger|between:1,15'   //上限1~15, 不需要require是因为有默认值15，不传就取默认值
                                                      //中间不要加空格！！否则不识别
    ];
}