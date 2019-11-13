<?php


namespace Yirius\Admin;


use Yirius\Admin\auth\Auth;
use Yirius\Admin\auth\Jwt;
use Yirius\Admin\widgets\Cache;
use Yirius\Admin\widgets\Send;
use Yirius\Admin\widgets\Tree;
use Yirius\Admin\widgets\Validate;
use Yirius\Admin\widgets\Widgets;

/**
 * Class ThinkerAdmin
 * @method static Send Send()
 * @method static Validate Validate()
 * @method static Tree Tree()
 * @method static Auth Auth()
 * @method static Jwt Jwt()
 * @method static Cache Cache()
 * @package Yirius\Admin
 */
class ThinkerAdmin
{
    /**
     * @var array
     */
    protected static $extends = [
        'send'     =>   Send::class,
        'validate' =>   Validate::class,
        'tree'     =>   Tree::class,
        'auth'     =>   Auth::class,
        'jwt'      =>   Jwt::class,
        'cache'    =>   Cache::class
    ];

    /**
     * @title      extend
     * @description 继承新的类库
     * @createtime 2019/11/13 11:05 下午
     * @param      $name
     * @param null $class
     * @author     yangyuance
     */
    public static function extend($name, $class = null)
    {
        if(is_array($name)){
            static::$extends = array_merge(static::$extends, array_change_key_case($name, CASE_LOWER));
        }else{
            if(!is_null($class)){
                static::$extends[strtolower($name)] = $class;
            }
        }
    }

    /**
     * @title      __callStatic
     * @description 触发自定义类库
     * @createtime 2019/11/13 10:58 下午
     * @param $name
     * @param $arguments
     * @return null
     * @author     yangyuance
     */
    public static function __callStatic($name, $arguments)
    {
        if(isset(static::$extends[strtolower($name)])){
            //判断是否存在该类
            $newClass = (new static::$extends[strtolower($name)]);
            //存在的话，判断参数
            if(method_exists($newClass, "setArguments")){
                $newClass->setArguments($arguments);
            }
            //返回实例化
            return $newClass;
        }else{
            return null;
        }
    }
}