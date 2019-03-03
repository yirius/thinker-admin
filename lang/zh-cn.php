<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/26
 * Time: 下午2:50
 */
return [
    //access_token
    "no access_token to access" => "您暂无授权",
    "no authority to access" => "您的授权已过期或无法使用",
    "do not have authority to access" => "您暂无访问{:url}的授权",

    //jwt
    "ensure there is a key in thinkeradmin's config" => "暂无加密key, 请您前往config中配置",
    "authorization has expired" => "很抱歉, 登录状态已过期, 您需要重新登录",
    "incorrect authorization" => "很抱歉, 登录签名校验不正确, 您需要重新登录",
    "need to relogin" => "很抱歉, 您需要重新登录",
    "need to relogin with some error" => "很抱歉, 您需要重新登录, 原因未知, 请联系客服人员",

    //login
    "incorrect verfiy" => "验证码输入不正确, 请您重新输入",
    "empty userinfo" => "暂无该用户信息",
    "incorrect username or password" => "用户登录账号密码错误",
    "login success" => "您已经成功登录，正在跳转..."
];