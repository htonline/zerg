<?php


namespace app\api\model;


use app\api\model\Banner as BannerModel;

//TP5默认类名与数据库里的表名一一映射，若不想，就要自己定义如下
//protected $table = 'category';
class Banner extends BaseModel
{
    //TP5默认类名与数据库里的表名一一映射，若不想，就要自己定义如下
    //protected $table = 'category';

    //隐藏功能封装
    protected $hidden = ['delete_time','update_time'];

    //定义关联函数
    public function items(){
        return $this->hasMany('BannerItem','banner_id','id');
    }

    //TODO:根据Banner ID号 获取Banner信息。
    //用这个id作为数据库的查询条件，把数据库里的相关系检索出来，并返回调用方
    public static function getBannerByID($id)
    {
        $banner = BannerModel::with(['items', 'items.img'])->find($id);
        return $banner;
    }

}