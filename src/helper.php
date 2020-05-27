<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 上午12:49
 */


use think\facade\Lang;
use think\facade\Route;

defined("DS") or define("DS", DIRECTORY_SEPARATOR);//目录分割的缩写
defined("THINKER_ROOT") or define("THINKER_ROOT", __DIR__);//当前composer包的地址

//load lang
Lang::load(dirname(__DIR__) . DS . "lang" . DS . Lang::detect() . ".php");

//add delete all
Route::rest("deleteall", ['delete', '', 'deleteall']);

//Restful路由
Route::resource(
    "restful/thinkeradmin/:restful",
    "\\Yirius\Admin\\admin\\restful\\:restful"
);
//便捷路由访问相关模块api
Route::any(
    "thinkeradmin/:controllername/:controlleraction",
    "\\Yirius\Admin\\admin\\controller\\:controllername@:controlleraction"
);

//加入以下console
\think\Console::addDefaultCommands([
    "Yirius\\Admin\\commands\\Cache",
    "Yirius\\Admin\\commands\\Runjobs",
    "Yirius\\Admin\\commands\\Init"
]);

//注册钩子
\think\facade\Hook::add('response_end', '\\Yirius\\Admin\\support\\hooks\\RespEnd');

/**
 * 初始化记录错误
 */
if(!function_exists("thinker_error")){
    function thinker_error(Exception $exception)
    {
        trace(
            "File: " . $exception->getFile() .
            "<br/>Line: " . $exception->getLine() .
            "<br/>Message: " . $exception->getMessage(),
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

/**
 * 初始化记录其他数据参数
 */
if(!function_exists("thinker_path_log")){
    function thinker_path_log($data, $path = "http")
    {
        $logConfig = config("log.");
        $logConfig['path'] = app()->getRuntimePath() . "log_" . $path . DIRECTORY_SEPARATOR;

        try{
            \think\facade\Log::init($logConfig)
                ->write(is_array($data) ? json_encode($data) : $data, "info", true);

            $logConfig['path'] = "";
            \think\facade\Log::init($logConfig);
        }catch (Exception $exception){
            $logConfig['path'] = "";
            \think\facade\Log::init($logConfig);

            thinker_error($exception);
        }
    }
}