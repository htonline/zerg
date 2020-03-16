<?php


namespace app\api\controller\v1;


use app\api\service\Order as OrderService;
use app\api\controller\BaseController;
use app\api\service\Token as TokenService;
use app\api\validate\IDMustBePostiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\PagingParameter;
use app\api\model\Order as OrderModel;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Controller;

class Order extends BaseController
{
    //用户在选择商品后，向API提交包含它所选择商品的相关信息
    //API在接收到信息后，需要检查订单相关商品的库存量
    //有库存，把订单数据存入数据库中，下单成功，并返回客户端消息，可以支付
    //调用支付接口，进行支付
    //还需要再次进行库存量检测。（毕竟允许在下单之后的一段时间内再支付）
    //服务器这边可以调用微信的支付接口进行支付
    //微信会返回给我们一个支付结果（异步）
    //成功：也需要进行库存量的检测（这一步可以省略）
    //成功，进行库存量的扣除；失败，微信会返回一个支付结果

    //只有用户可以访问
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getDetail, getSummaryByUser'],
    ];

    //查询订单简要信息               分页数      每一页显示的个数
    public function getSummaryByUser($page=1, $size=15)
    {
        //可以直接从令牌中获取用户的uid,所以不用传.
        (new PagingParameter())->goCheck();
        $uid = \app\api\service\Token::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size); //返回的是一个对象
        if($pagingOrders->isEmpty()){
            return [
                'data' => [],
                'current_page' => $pagingOrders->getCurrentPage() //这个方法不一定能用
            ];
        }
        $data = $pagingOrders->hidden(['snap_items','snap_address','prepay_id'])->toArray();
        return [
            'data' => $data,
            'current_page' => $pagingOrders->getCurrentPage()
        ];
    }

    //历史订单中跳转详情页面
    public function getDetail($id)
    {
        (new IDMustBePostiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if(!$orderDetail)
        {
            throw new OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }


    //下单接口
    public function placeOrder()
    {
        (new OrderPlace())->goCheck();
        //接收客户端传过来的参数->input,因为products是个数组，所有必须在后面加'/a'
        //post好像是因为传url时定的。其他有get之类的
        $products = input('post.products/a');

        //拿到用户的uid.确定下单用户
        $uid = TokenService::getCurrentUid();

        $order = new OrderService();
        $status = $order->place($uid, $products);
        return $status;



    }




}