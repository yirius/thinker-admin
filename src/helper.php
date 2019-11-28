<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 上午12:49
 */


defined("DS") or define("DS", DIRECTORY_SEPARATOR);//目录分割的缩写
defined("THINKER_ROOT") or define("THINKER_ROOT", __DIR__);//当前composer包的地址

/**
 * 初始化记录错误
 */
if(!function_exists("thinker_error")){
    function thinker_error(Exception $exception)
    {
        trace(
            "File: " . $exception->getFile() .
            "||Line: " . $exception->getLine() .
            "||Message: " . $exception->getMessage(),
            "error"
        );
    }
}

/**
 * 便捷记录操作
 */
if(!function_exists("thinker_log")){
    function thinker_log(array $tokenInfo, $desc, $isLogin = false)
    {
        \Yirius\Admin\route\model\TeAdminLogs::addLog($tokenInfo, $desc, $isLogin);
    }
}

//load lang
\think\facade\Lang::load(dirname(__DIR__) . DS . "lang" . DS . \think\facade\Lang::detect() . ".php");

//add delete all
\think\facade\Route::rest("deleteall", ['delete', '', 'deleteall']);

//预定义所有的
\think\facade\Route::resource(
    "restful/thinkeradmin/:restful",
    "\\Yirius\Admin\\route\\restful\\:restful"
);
//便捷路由访问相关模块api
\think\facade\Route::any(
    "thinkeradmin/:controllername/:controlleraction",
    "\\Yirius\Admin\\route\\controller\\:controllername@:controlleraction"
);

//加入以下console
\think\Console::addDefaultCommands([
    "Yirius\\Admin\\commands\\Cache",
    "Yirius\\Admin\\commands\\Runjobs",
    "Yirius\\Admin\\commands\\Init"
]);

//注册钩子
\think\facade\Hook::add('app_init','\\Yirius\\Admin\\hooks\\AppInit');
\think\facade\Hook::add('response_end', '\\Yirius\\Admin\\hooks\\RespEnd');