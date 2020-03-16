<?php


namespace app\api\model;

use app\api\model\Product as ProductModel;


class Product extends BaseModel
{
    protected $hidden = [
        'delete_time','main_img_id','pivot',
        'from','category_id','create_time',
        'update_time'
    ];
    //pivot: 多对多关系中间表，TP5默认自动加的属性

    //读取器
    public function getMainImgUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }

    //查询指定数量的商品，并按某个字段倒叙排列
    public static function getMostRecent($count){
        $products = self::limit($count)                //限制数量
            ->order('create_time','desc') //字段，倒叙
            ->select();
        return $products;
    }

    //查询属于某个分类里的所有商品
    public static function getProductsByCategoryID($categoryID)
    {
        $products = self::where('category_id','=', $categoryID)->select();
        return $products;
    }

    //关联ProductImage
    public function imgs(){
        return $this->hasMany('ProductImage','product_id','id');
    }

    //关联ProductProperty
    public function properties(){
        return $this->hasMany('ProductProperty','product_id','id');
    }

    //查询详情页里包含的数据
    public static function getProductDetail($id){
        //$product = self::with(['imgs.imgurl','properties'])->find($id);
        $product = self::with([
            'imgs' => function($query){
                $query->with(['imgUrl'])
                      ->order('order','asc');
                      //里面那个order是字段，asc是升序
            }
        ])
            ->with(['properties'])
            ->find($id);
        return $product;
    }


}