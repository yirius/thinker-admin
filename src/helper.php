<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 上午12:49
 */

defined("DS") or define("DS", DIRECTORY_SEPARATOR);//目录分割的缩写
defined("THINKER_ROOT") or define("THINKER_ROOT", __DIR__);//当前composer包的地址

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
    "Yirius\\Admin\\command\\Cache",
    "Yirius\\Admin\\command\\Menu",
    "Yirius\\Admin\\command\\Init"
]);