<?php


namespace app\api\model;

use app\api\model\Category as CategoryModel;

class Category extends BaseModel
{
    protected $hidden = ['delete_time','update_time','create_time'];

    public function img(){
        return $this->belongsTo('Image','topic_img_id','id');
    }

    public static function getCategory(){
        $category = self::with('img')->select();
        return $category;
    }



}