<?php


namespace app\api\model;


use think\Model;

class BannerItem extends BaseModel
{
    //隐藏功能封装
    protected $hidden = ['id','img_id','banner_id','update_time','delete_time'];

    //定义关联函数
    public function img(){
        return $this->belongsTo('Image', 'img_id', 'id');
    }

}