<?php


namespace app\api\model;


use think\Model;

class Image extends BaseModel
{
    //隐藏功能封装
    protected $hidden = ['id','from','delete_time','update_time'];

    //定义读取器
    public function getUrlAttr($value, $data){
        return $this->prefixImgUrl($value, $data);
    }

}