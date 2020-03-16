<?php


namespace app\api\service;


use app\api\service\Token as TokenService;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    //Token有两种，一种UserToken ,一个APPToken。
    //生成Token令牌
    public static function generateToken(){
        //无意义的32个字符组成一组随机字符串
        $randChars = getRandChar(32);
        //加强安全性,用三组字符串，进行md5加密
        $timetamp = $_SERVER['REQUEST_TIME_FLOAT']; //php内置，获取当前访问的时间戳
        //salt 盐 特殊加密信息，随机字符串
        $salt = config('secure.token_salt');

        return md5($randChars.$timetamp.$salt);
    }

    //（通用）传入一个参数，这个参数是指定获取用户Uid或者openID。。。[缓存中的哪一个变量]
    public static function getCurrentTokenVar($key){
        $token = Request::instance()->header('token');//从http头里获取token
        $vars = Cache::get($token); //从缓存中读取对应token中相应的value值
        if(!$vars){
            throw new TokenException();
        }
        else{
            if (!is_array($vars))//如果它不是数组
            {
                //字符串转换成数组
                $vars = json_decode($vars, true);
            }
            //如果数组里面存在指定的key话，就返回回去
            if (array_key_exists($key,$vars)){
                return $vars[$key];
            }
            else{
                throw new Exception('尝试获取的Token变量并不存在');
            }
        }
    }

    //根据令牌获取Uid号
    public static function getCurrentUid(){
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    //封装前置方法,管理员和用户都可以访问
    public static function needPrimaryScope()
    {
        $scope = TokenService::getCurrentTokenVar('scope');
        if($scope){
            if($scope >= ScopeEnum::User){
                return true;
            }
            else{
                throw new ForbiddenException();
            }
        }
        else{
            throw new TokenException();
        }
    }

    //封装前置方法，只有用户才能访问的接口权限
    public static function needExclusiveScope()
    {
        $scope = TokenService::getCurrentTokenVar('scope');
        if($scope){
            if($scope == ScopeEnum::User){
                return true;
            }
            else{
                throw new ForbiddenException();
            }
        }
        else{
            throw new TokenException();
        }
    }

    public static function verifyToken($token)
    {
//        查询token是否还存在缓存里面
        $exist = Cache::get($token);
        if ($exist) {
            return true;
        } else {
            return false;
        }
    }

}