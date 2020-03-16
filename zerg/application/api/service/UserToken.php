<?php


namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    //UserToken构造函数
    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'),
            $this->wxAppID,$this->wxAppSecret,$this->code);//占位符%s,就跟C里面printf差不多吧
    }

    public function get(){
        $result = curl_get($this->wxLoginUrl);//发送HTTP请求,返回一个字符串
        $wxResult = json_decode($result, true);//把字符串变成数组。加true是为了变成数组，否则就成了对象
        if(empty($wxResult)){
            throw new Exception('获取session_key及openID时异常，微信内部错误');//服务器内部异常，不想返回客户端。
        }
        else{
            $loginFail = array_key_exists('errcode',$wxResult);
            //查看是否包含errcode来判断登录失败还是成功。因为微信返还状态码无论成功还是失败都是200
            //没道理可讲，经验性的。（双重验证）
            if($loginFail){
                $this->processLoginError($wxResult);
            }
            else{
                return $this->grantToken($wxResult);
            }
        }
    }

    //返回成功，生成令牌
    private function grantToken($wxResult){
        //拿到openid
        //数据库里看一下，这个openid是不是已经存在
        //已经存在说明用户已经生成，不处理。不存在说明用户没生成，要新增一条user记录,相当于注册新用户
        //生成令牌，准备缓存数据，写入缓存
        //把令牌返回客户端去
        //key:令牌(随机字符串，复杂一点)
        //value:wxResult,uid,scope(用户身份) [访问缓存比访问数据库速度快]
        $openid = $wxResult['openid'];
        $user = UserModel::getByOpenID($openid);
        if($user){
            $uid = $user->id;
        }
        else{
            $uid = $this->newUser($openid);
        }
        $cacheValue = $this->prepareCacheValue($wxResult,$uid);
        $token = $this->saveToCache($cacheValue);
        return $token;
    }

    //写入缓存
    private function saveToCache($cacheValue){
        $key = self::generateToken();
        $value = json_encode($cacheValue);//将数组转换成字符串
        $expire_in = config('setting.token_expire_in');

        $request = cache($key,$value,$expire_in);//TP5自带缓存,文件系统
        if(!$request){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $key;
    }

    //准备一系列value的数据
    private function prepareCacheValue($wxResult, $uid){
        $cacheValue = $wxResult;
        $cacheValue['uid'] = $uid;
        $cacheValue['scope'] = ScopeEnum::User;
        return $cacheValue;
    }

    //插入用户
    private function newUser($openid){
        $user = UserModel::create([
            'openid' => $openid
        ]);
        return $user->id;
    }

    //处理异常，封装方法
    private function processLoginError($wxResult){
        throw new WeChatException([
            'msg' => $wxResult['errmsg'],//将wxResult里的errmsg属性返回客户端里去。
            'errorCode'=> $wxResult['errcode']
        ]);
    }

}