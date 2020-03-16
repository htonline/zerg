<?php
//接口信息
//Banenr控制器
namespace app\api\controller\v1;

use app\api\validate\IDMustBePostiveInt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;
use think\Exception;//别名

//导入库要使用自动补全功能，自己写容易出错！！！！！
class Banner
{
    /*
     * 获取指定id的banner信息
     * @url /banner/:id
     * @http GET
     * @id banner的id号
     *
     * */
    public function getBanner($id)
    {
        (new IDMustBePostiveInt())->goCheck();//拦截器

        $banner = BannerModel::getBannerByID($id);
        if(!$banner){
            throw new BannerMissException();
        }
        return $banner;
    }

}