<?php


namespace app\api\service;


use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\ProductException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;


class Order
{
    //订单的商品列表，即客户端传递过来的products参数
    protected $oProducts;

    //真实的商品信息（从数据库中取出来的，包括库存量）
    protected $products;

    protected $uid;

    public function place($uid, $oProducts) //给谁下单，下了什么(客户端传过来的)
    {
        //oProducts和products作对比
        //products从数据库中查询出来
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid = $uid;    //用户id号
        $status = $this->getOrderStatus();
        if(!$status['pass'])
        {
            //如果库存量检测不通过，新增一个属性变量order_id并标记为-1
            $status['order_id'] = -1;
            return $status;
        }

        //开始创建订单
        $orderSnap = $this->snapOrder($status);
        $oredr = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $oredr;
    }

    //生产订单数据，写入数据库
    private function createOrder($snap)
    {
        //防止代码运行到一半因断电什么的停了,会被当成一整个事物
        Db::startTrans();
        try
        {
            //$orderNo = $this->makeOrderNo();
            $orderNo = self::makeOrderNo();
            $order = new \app\api\model\Order();

            //赋值
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);

            //写入数据库
            $order->save();

            $orderID = $order->id;
            $create_time = $order->create_time;

            //修改oProduct，然后保存
            foreach ($this->oProducts as &$p) //oProducts下的每一个子项 ,&地址符，修改操作
            {
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts); //保存数组
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];
        }
        catch (Exception $ex)
        {
            //一旦发生错误，回滚一遍
            Db::rollback();
            throw $ex;
        }
    }

    //生产订单编号(年.月.日,微妙数...balabala，减少重复！很多种)
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2020] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }


    //生产订单快照
    private function snapOrder($status)
    {
        //初始化
        $snap = [
            'orderPrice' => 0,      //订单总价格
            'totalCount' => 0,      //订单商品总数量,不是商品种类的总数量（3样商品，总共10件商品）
            'pStatus' => [],        //订单下所有商品的状态
            'snapAddress' => null,  //快照地址，为空
            'snapName' => '',       //订单概要名字（一个订单下有多个商品，选一个做代表的）
            'snapImg' => ''         //订单概要图片
        ];

        //赋值
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = json_encode($this->getUserAddress()); //返回的是数组，数组存到数据库里去，需要数组序列化成json字符串
        $snap['snapName'] = $this->products[0]['name'];              //去订单里的第一个商品做概要
        $snap['snapImg'] = $this->products[0]['main_img_url'];

        //如果订单下商品数量大于1，就在名字后面加个'等'字
        if(count($this->products) > 1)
        {
            $snap['snapName'] .= '等';
        }
    }

    //获取用户地址
    private function getUserAddress()
    {
        //查找
        $userAddress = UserAddress::where('user_id','=',$this->uid)->find();//UserAddress表里与user关联的外键是user_id
        if(!$userAddress){
            throw new UserException([
                'msg' => '用户收货地址不存在，下单失败',
                'errCode' => 60001,
            ]);
        }
        else{
            return $userAddress->toArray();     //模型查询出来的是一个对象，转换成数组形式
        }

    }


    //获取订单真实状态
    public function getOrderStatus()
    {
        $status = [
            'pass' => true,         //默认通过，只要有一个库存量是不够的，pass就将被赋为false
            'orderPrice' => 0,      //订单总价格默认为0
            'pStatusArray' => [],   //保存订单所有商品的详细信息
            'totalCount' => 0       //订单商品总数量,不是商品种类的总数量（3样商品，总共10件商品）
        ];
        //库存量对比（两个数组间的对比）
        foreach ($this->oProducts as $oProduct)  //$oProduct表示单个oProduct
        {
            $pStatus = $this->getProductStatus($oProduct['product_id'],$oProduct['count'],$this->products);
            if(!$pStatus['haveStock']){
                $status['pass'] = false;  //有一个不通过，整体就不通过
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'], $pStatus);
        }
        return $status;
    }

    //封装（两个数组间的对比）方法     购买的商品ID，对应ID购买数量，根据oPID查找对应的商品
    private function getProductStatus($oPID, $oCount, $products)
    {
        $pIndex = -1;

        //保存订单里的某一个商品的详细信息
        $pStatus = [
            'id'=> null,
            'haveStock' => false,  //库存量默认为空
            'count' => 0,          //订单请求数量
            'name' => '',          //商品名字
            'totalPrice' => 0      //某一类商品的单价*数量的总价格,与orderPrice有差别
        ];

        for($i=0; $i<count($products); $i++){    //这个count是php的一个方法
            if($oPID == $products[$i]['id']){
                $pIndex = $i;
            }
        }

        //如果传过来的oPID不存在，抛出异常
        if($pIndex == -1){
            //客户端传递的product_id有可能不存在
            throw new OrderException([
                'msg' => 'id为'.$oPID.'商品不存在，创建订单失败'
            ]);
        }
        else{
            $product = $products[$pIndex];
            //完成pStatus数组
            $pStatus['id'] = $product['id'];
            $pStatus['count'] = $oCount;
            $pStatus['name'] = $product['name'];
            $pStatus['totalPrice'] = $product['price'] * $oCount;

            if($product['stock'] - $oCount >= 0 ){
                $pStatus['haveStock'] = true;
            }
        }
        return $pStatus;

    }

    //根据订单信息查询真实商品信息
    private function getProductsByOrder($oProducts)
    {
//        foreach ($oProducts as $oProduct){}
//            //循环查询数据库不可取
        $oPIDs = [];
        foreach ($oProducts as $item){                      //循环遍历oProducts
            array_push($oPIDs, $item['product_id']); //将每一个读取出来的products_id push到oPID数组里面去
        }
        //得到订单里所有商品id号的数组
        //根据这个数组查询商品真实信息

        //根据订单里的商品id号查询出真实的商品信息来。
        $products = Product::all($oPIDs)
            ->visible(['id','price','stock','name','main_img_url'])
            ->toArray();//转化成数组
        return $products;
    }

}