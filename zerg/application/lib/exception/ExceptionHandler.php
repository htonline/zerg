<?php


namespace app\lib\exception;
//自定义异常结果 覆盖

use think\Exception;
use think\exception\Handle;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;
    //需要返回客户端当前请求的URL路径

    public function render(\Exception $e) //加'\'说明这个是基类Exception
    {
        if($e instanceof BaseException){
            //如果是自定义的异常,$e如果符合BaseException.
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        }
        else{
//            Config::get('app_debug');
            if(config('app_debug'))
            {
                return parent::render($e); //接着用TP5自带的错误返回页面.
            }
            else
            {
                $this->code = 500;
                $this->msg = '服务器内部错误，不想告诉你';
                $this->errorCode = 999;
                $this->recordErrorLog($e);//日志记录
            }
        }
        $request = Request::instance();

        //定义一个返回的结果
        $result = [
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => $request->url()
        ];
        return json($result, $this->code);
    }

    //日志
    private function recordErrorLog(\Exception $e)
    {
        //日志初始化
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH,
            'level' => ['error'] //如果级别低于error，是不会记录进去的
        ]);
        //日志记录，级别
        Log::record($e->getMessage(),'error');
    }

}