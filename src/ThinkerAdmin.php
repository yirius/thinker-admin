<?php


namespace Yirius\Admin;


use Yirius\Admin\auth\Auth;
use Yirius\Admin\auth\Jwt;
use Yirius\Admin\form\ThinkerForm;
use Yirius\Admin\table\ThinkerTable;
use Yirius\Admin\widgets\Cache;
use Yirius\Admin\widgets\Encrypt;
use Yirius\Admin\widgets\File;
use Yirius\Admin\widgets\Http;
use Yirius\Admin\widgets\Redis;
use Yirius\Admin\widgets\Send;
use Yirius\Admin\widgets\Tools;
use Yirius\Admin\widgets\Tree;
use Yirius\Admin\widgets\Validate;
use Yirius\Admin\widgets\Widgets;

/**
 * Class ThinkerAdmin
 *
 * 以下是widgets
 * @method static Send Send()
 * @method static Validate Validate()
 * @method static Tree Tree()
 * @method static Auth Auth()
 * @method static Jwt Jwt()
 * @method static Cache Cache()
 * @method static Tools Tools()
 * @method static Http Http()
 * @method static Encrypt Encrypt()
 * @method static File File()
 * @method static Redis Redis()
 *
 * @package Yirius\Admin
 */
class ThinkerAdmin
{
    /**
     * @var array
     */
    protected static $extends = [
        //widgets添加
        'send'     =>   Send::class,
        'validate' =>   Validate::class,
        'tree'     =>   Tree::class,
        'auth'     =>   Auth::class,
        'jwt'      =>   Jwt::class,
        'cache'    =>   Cache::class,
        'tools'    =>   Tools::class,
        'http'     =>   Http::class,
        'encrypt'  =>   Encrypt::class,
        'file'     =>   File::class,
        'redis'    =>   Redis::class,
    ];

    /**
     *
     * @var array
     */
    protected static $script = [
        'file' => [],
        'use' => [],
        'script' => [],
        'template' => []
    ];

    /**
     * @var array
     */
    protected static $style = [
        'file' => [],
        'style' => []
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
     * @title      Table
     * @description 快速初始化表格
     * @createtime 2019/11/14 5:07 下午
     * @param callable|null $callback
     * @return ThinkerTable
     * @author     yangyuance
     */
    public static function Table(callable $callback = null)
    {
        return (new ThinkerTable($callback));
    }

    /**
     * @title      Form
     * @description 快速初始化表单
     * @createtime 2019/11/16 9:50 下午
     * @param callable|null $callback
     * @return ThinkerForm
     * @author     yangyuance
     */
    public static function Form(callable $callback = null)
    {
        return (new ThinkerForm($callback));
    }

    /**
     * @title      style
     * @description
     * @createtime 2019/11/14 6:32 下午
     * @param      $style
     * @param bool $isFile
     * @author     yangyuance
     */
    public static function style($style, $isFile = false)
    {
        if ($isFile === false) {
            self::$style['style'][] = $style;
        } else {
            self::$style['file'][] = $style;
        }
    }

    /**
     * @return array
     */
    public static function getStyle()
    {
        return self::$style;
    }

    /**
     * @title      script
     * @description
     * @createtime 2019/11/14 6:30 下午
     * @param      $script
     * @param bool $isFile
     * @param bool $isUse
     * @param bool $isTemplate
     * @author     yangyuance
     */
    public static function script($script, $isFile = false, $isUse = false, $isTemplate = false)
    {
        if(!$isFile && !$isUse && !$isTemplate){
            self::$script['script'][] = $script;
        }else if($isTemplate){
            self::$script['template'][] = $script;
        }else if($isUse){
            self::$script['use'][] = $script;
        }else{
            self::$script['file'][] = $script;
        }
    }

    /**
     * @return array
     */
    public static function getScript()
    {
        return self::$script;
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
            //获取到父类名称，判断是否一个widget
            $parentClassName = get_parent_class(static::$extends[strtolower($name)]);

            //判断是否存在该类
            if($parentClassName == "Yirius\\Admin\\widgets\\Widgets"){
                unset($parentClassName);
                return static::$extends[strtolower($name)]::getInstance(
                    isset($arguments[0]) ? $arguments[0] : null
                );
            }else{
                unset($parentClassName);
                //否则是其他的参数
                $newClass = (new static::$extends[strtolower($name)]);
                //存在的话，判断参数
                if(method_exists($newClass, "setArguments")){
                    $newClass->setArguments($arguments);
                }
                //返回实例化
                return $newClass;
            }
        }else{
            return null;
        }
    }
}