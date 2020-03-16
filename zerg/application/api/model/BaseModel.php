<?php


namespace app\api\model;
//模型共有特性

use think\Model;

class BaseModel extends Model
{   //基类方法子类自动继承
    /*读取器URL.
     * value获得相对路径，data获得所有数据
     * if   from = 1：代表图片来自本地，配置文件+相对路径=可以取到图片的完整url路径。
     * else from = 2：代表图片来自网络，其本身路径已经完整。直接返回
     * ->getUrlAttr,这个名字会自动识别成一个读取器，
     * 所有有url命名的字段都会自动调用这个读取器
    */
    protected function prefixImgUrl($value,$data){
        $finalUrl = $value;
        if($data['from'] == 1){
            $finalUrl = config('setting.img_prefix').$value;
        }
        return $finalUrl;
    }
}