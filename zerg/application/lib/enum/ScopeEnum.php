<?php


namespace app\lib\enum;


class ScopeEnum
{
    //权限控制
    //模拟枚举类型,数值任意，但Super>User
    //scope=16 代表APP用户的权限数值
    //scope=32 代表CMS（管理员）用户的权限数值
    const User = 16;

    const Super = 32;

}