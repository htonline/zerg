<?php


namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\User;
use app\api\model\UserAddress;
use app\api\validate\AddressNew;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;
use think\Controller;

class Address extends BaseController
{
    //TP5自带前置操作定义（必须，不能省）
    //管理员和用户都可以访问
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress,getUserAddress']
    ];

    public function getUserAddress(){
        $uid = \app\api\service\Token::getCurrentUid();
        $userAddress = UserAddress::where('user_id', $uid)->find();
        if(!$userAddress){
            throw new UserException([
                'msg'=>'用户地址不存在',
                'errorCode' => 60001
            ]);
        }
    }

    public function createOrUpdateAddress()
    {
        $validate = new AddressNew();
        $validate->goCheck();
        //根据Token获取用户uid
        //根据uid查找用户数据，判断用户是否存在，如果不存在抛出异常
        //获取用户从客户端提交来的地址信息
        //根据用户地址信息是否存在判断是添加地址还是更新

        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if(!$user){
            throw new UserException();
        }

        $dataArray = $validate->getDataByRule(input('post.'));//获取post客户端里所有参数

        $userAddress = $user->address;  //读取User模型下关联的一个属性
        if(!$userAddress)
        {
            //用模型的关联模型新增一条记录
            $user->address()->save($dataArray);
        }
        else{
            //用模型的关联模型更新一条记录
            $user->address->save($dataArray);
        }
        //return $user;
        //return 'success';
        return json(new SuccessMessage(),201);
    }

}