<?php


namespace Yirius\Admin;


use Yirius\Admin\config\ThinkerProperties;
use Yirius\Admin\renders\ThinkerForm;
use Yirius\Admin\renders\ThinkerTable;
use Yirius\Admin\services\RedisService;
use Yirius\Admin\services\UploadService;
use Yirius\Admin\widgets\ThinkerCache;
use Yirius\Admin\widgets\ThinkerEncrypt;
use Yirius\Admin\widgets\ThinkerFile;
use Yirius\Admin\widgets\ThinkerHttp;
use Yirius\Admin\widgets\ThinkerJwt;
use Yirius\Admin\widgets\ThinkerResponse;
use Yirius\Admin\widgets\ThinkerTools;
use Yirius\Admin\widgets\ThinkerTree;
use Yirius\Admin\widgets\ThinkerValidate;

/**
 * Class ThinkerAdmin
 * @package Yirius\Admin
 * @method static ThinkerCache cache()
 * @method static ThinkerEncrypt encrypt()
 * @method static ThinkerFile file()
 * @method static ThinkerJwt jwt()
 * @method static ThinkerResponse response()
 * @method static ThinkerTools tools()
 * @method static ThinkerTree tree()
 * @method static ThinkerValidate validate()
 *
 * @method static RedisService redis()
 * @method static UploadService upload()
 *
 * @method static ThinkerProperties properties()
 */
class ThinkerAdmin
{
    /**
     * @var array
     */
    protected static $extends = [
        //widgets添加
        'cache'      =>   ThinkerCache::class,
        'encrypt'    =>   ThinkerEncrypt::class,
        'file'       =>   ThinkerFile::class,
        'jwt'        =>   ThinkerJwt::class,
        'response'   =>   ThinkerResponse::class,
        'tools'      =>   ThinkerTools::class,
        'tree'       =>   ThinkerTree::class,
        'validate'   =>   ThinkerValidate::class,

        //服务
        'redis'      =>   RedisService::class,
        'upload'     =>   UploadService::class,

        //配置
        'properties' =>   ThinkerProperties::class
    ];

    protected static $instanceExtends = [];

    /**
     * @title      http
     * @description
     * @createtime 2020/5/27 12:57 下午
     * @return ThinkerHttp
     * @author     yangyuance
     */
    public function http() {
        return new ThinkerHttp();
    }

    private static $classMethod = null;

    /**
     * @title      setClassMethod
     * @description
     * @createtime 2020/5/27 1:33 下午
     * @param $classMethod
     * @author     yangyuance
     */
    public static function setClassMethod($classMethod) {
        self::$classMethod = $classMethod;
    }

    /**
     * @return null
     */
    public static function getClassMethod()
    {
        return self::$classMethod;
    }

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
     * @title      Table
     * @description 快速初始化表格
     * @createtime 2019/11/14 5:07 下午
     * @param callable|null $callback
     * @return ThinkerTable
     * @author     yangyuance
     */
    public static function table(callable $callback = null)
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
    public static function form(callable $callback = null)
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
        $name = strtolower($name);
        if(isset(self::$extends[$name])){
            if(!isset(self::$instanceExtends[$name])) {
                self::$instanceExtends[$name] = new self::$extends[$name]($arguments);
            }
            return self::$instanceExtends[$name];
        }else{
            (new ThinkerResponse())->msg("执行ThinkerAdmin方法".$name."错误")->fail();
        }
    }
}