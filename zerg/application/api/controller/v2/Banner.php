<?php

//Banenr控制器
namespace app\api\controller\v2;

//别名

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
        return 'This is v2 Version';
    }

}