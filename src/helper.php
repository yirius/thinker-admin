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

//登录等非restful api能使用的
\think\facade\Route::alias("thinkeradmin", "\\Yirius\\Admin\\controller\\Admin", ['deny_ext' => 'php|.htacess']);
//System's Controller
\think\facade\Route::alias("thinkersystem", "\\Yirius\\Admin\\controller\\System", ['deny_ext' => 'php|.htacess']);
//Cms's Controller
\think\facade\Route::alias("thinkercms", "\\Yirius\\Admin\\controller\\Cms", ['deny_ext' => 'php|.htacess']);

//add delete all
\think\facade\Route::rest("deleteall", ['delete', '', 'deleteall']);

//restful api
\think\facade\Route::resource("restful/adminmenu", "\\Yirius\\Admin\\model\\restful\\AdminMenu");
\think\facade\Route::resource("restful/adminrule", "\\Yirius\\Admin\\model\\restful\\AdminRule");
\think\facade\Route::resource("restful/adminrole", "\\Yirius\\Admin\\model\\restful\\AdminRole");
\think\facade\Route::resource("restful/adminmember", "\\Yirius\\Admin\\model\\restful\\AdminMember");
\think\facade\Route::resource("restful/cmsmodels", "\\Yirius\\Admin\\model\\restful\\CmsModels");
\think\facade\Route::resource("restful/cmscolumns", "\\Yirius\\Admin\\model\\restful\\CmsColumns");


//加入以下console
\think\Console::addDefaultCommands([
    "Yirius\\Admin\\command\\Cache",
    "Yirius\\Admin\\command\\Menu",
    "Yirius\\Admin\\command\\Init"
]);