<?php


namespace app\api\model;


use app\api\model\Theme as ThemeModel;

class Theme extends BaseModel
{
    protected $hidden = ['delete_time','topic_img_id','head_img_id','update_time'];

    public static function getThemeByID($ids){
        $result = ThemeModel::with('topicImg,headImg')->select($ids);
        return $result;
    }

    public function topicImg()
    {
        return $this->belongsTo('Image','topic_img_id','id');
    }
    public function headImg()
    {
        return $this->belongsTo('Image','head_img_id','id');
    }
    public function products(){
        return $this->belongsToMany('Product','theme_product','product_id','theme_id');
    }

    //要加static才能被调用
    public static function getThemeWithProducts($id){
        $theme = ThemeModel::with('products,topicImg,headImg')->find($id);
        //那些字符串都是上面定义好才能用
        return $theme;
    }

}