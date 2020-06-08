<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 上午12:49
 */


use think\facade\Lang;
use think\facade\Route;
use Yirius\Admin\admin\model\AdminLogsModel;

defined("DS") or define("DS", DIRECTORY_SEPARATOR);//目录分割的缩写
defined("THINKER_ROOT") or define("THINKER_ROOT", __DIR__);//当前composer包的地址

//load lang
Lang::load(dirname(__DIR__) . DS . "lang" . DS . Lang::detect() . ".php");

//add delete all
Route::rest("deleteall", ['delete', '', 'deleteall']);
Route::rest("json", ['get', '/json/:id/:field', 'json']);
Route::rest("jsonput", ['put', '/json/:id/:field', 'jsonput']);

$prefixName = "\\Yirius\Admin\\admin\\";

//Restful路由
Route::group("restful/thinkeradmin", function() use($prefixName){
    Route::any("/TeAdminRules/quickadd", $prefixName."restful\\TeAdminRules@quickadd");
});
Route::resource("restful/thinkeradmin/:restful", $prefixName."restful\\:restful");

//加入指定分组
Route::group("thinkeradmin", function() use($prefixName){
    Route::any("Admin/:controlleraction", $prefixName."controller\\Admin@:controlleraction");
    Route::any("admin/:controlleraction", $prefixName."controller\\Admin@:controlleraction");

    Route::any("System/:controlleraction", $prefixName."controller\\System@:controlleraction");
    Route::any("system/:controlleraction", $prefixName."controller\\System@:controlleraction");

    Route::any("Log/:controlleraction", $prefixName."controller\\Log@:controlleraction");
    Route::any("log/:controlleraction", $prefixName."controller\\Log@:controlleraction");
});

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
        AdminLogsModel::addLog($tokenInfo, $desc, $isLogin);
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