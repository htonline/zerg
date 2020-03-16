<?php


namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category
{
    public function getAllCategories(){
        //没有参数，所以不需要参数校验
        $categories = CategoryModel::getCategory();
        if($categories->isEmpty()){
            throw new CategoryException();
        }
        return $categories;
    }
}