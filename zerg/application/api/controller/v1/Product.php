<?php


namespace app\api\controller\v1;

use app\api\model\Product as ProductModel;
use app\api\validate\Count;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\ProductException;

class Product
{
    //获取最近商品
    //count新品数量
    public function getRecent($count=15){
        (new Count())->goCheck();
        $products = ProductModel::getMostRecent($count);

        //判空（细节）
        if($products->isEmpty()){
            throw new ProductException();
        }
        $products = $products->hidden(['summary']);//临时隐藏元素
        return $products;
    }

    //获取分类里的商品
    public function getAllInCategory($id){
        (new IDMustBePostiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryID($id);
        if($products->isEmpty()){
            throw new ProductException();
        }
        $products = $products->hidden(['summary']);
        return $products;
    }

    //获取商品详情页面
    public function getOne($id){
        (new IDMustBePostiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);
        if(!$product){
            throw new ProductException();
        }
        return $product;
    }

    //删除一个商品，肯定不是任何一个人都可以调用的。所以要对用户进行分组
    public function deleteOne($id){

    }

}