<?php


namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = ['user_id','delete_time','update_time'];

    //读取器 格式化snap_items 序列化
    public function getSnapItemsAttr($value)
    {
        if(empty($value))
        {
            return null;
        }
        return json_decode($value);
    }
    //读取器 格式化
    public function getSnapAddressAttr($value)
    {
        if(empty($value))
        {
            return null;
        }
        return json_decode($value);
    }

    //自动写入时间戳
    protected $autoWriteTimestamp = true;

    //查询订单简要信息
    public static function getSummaryByUser($uid, $page=1, $size=15)
    {
        $pagingData = self::where('user_id','=', $uid)
            ->order('create_time desc')
            ->paginate($size,true,['page' => $page]);
        //TP5自带分页查询, 参数：页数，是否要总记录数，true:简洁模式（不要总记录数） false：不采用简洁模式（要总记录数）,当前页
        //返回的是一个对象Paginator
        //paginate == find selects;
        return $pagingData;
    }

}