<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 下午4:01
 */

namespace Yirius\Admin;


use Yirius\Admin\auth\Auth;
use Yirius\Admin\extend\Adminjwt;
use Yirius\Admin\extend\Tools;
use Yirius\Admin\form\Form;
use Yirius\Admin\layout\PageView;
use Yirius\Admin\table\Table;

class Admin
{
    /**
     * use VERSION to verfiy thinkphp-admin extends
     */
    const VERSION = "1.0.0";

    /**
     *
     * @var array
     */
    protected static $script = [
        'file' => [],
        'use' => [],
        'script' => []
    ];

    /**
     * @var array
     */
    protected static $style = [
        'file' => [],
        'style' => []
    ];

    /**
     * @title version
     * @description
     * @createtime 2019/2/25 下午9:28
     * @return string
     */
    public static function version()
    {
        return "ThinkerAdmin Version: " . self::VERSION;
    }

    /**
     * @title form
     * @description
     * @createtime 2019/2/25 下午6:41
     * @param $formName
     * @param \Closure|null $callable
     * @return Form
     * @throws \Exception
     */
    public static function form($formName, \Closure $callable = null)
    {
        return (new Form($formName, $callable));
    }

    /**
     * @title table
     * @description
     * @createtime 2019/2/26 下午3:42
     * @param $tableName
     * @param \Closure|null $callable
     * @return Table
     */
    public static function table($tableName, \Closure $callable = null)
    {
        return (new Table($tableName, $callable));
    }

    /**
     * @title pageView
     * @description
     * @createtime 2019/2/24 下午4:49
     * @param \Closure|null $callable
     * @return PageView
     */
    public static function pageView(\Closure $callable = null)
    {
        return (new PageView($callable));
    }

    /**
     * @title auth
     * @description Auth Static New
     * @createtime 2019/2/21 下午10:41
     * @param array|null $config
     * @return Auth
     */
    public static function auth(array $config = null){
        return (new Auth($config));
    }

    /**
     * @title tools
     * @description Tools New Static
     * @createtime 2019/2/21 下午10:41
     * @return Tools
     */
    public static function tools()
    {
        return (new Tools());
    }

    /**
     * @title jwt
     * @description
     * @createtime 2019/2/22 上午11:46
     * @return Adminjwt
     */
    public static function jwt(){
        return (new Adminjwt());
    }

    /**
     * @title script
     * @description
     * @createtime 2019/2/25 下午10:47
     * @param $script
     * @param bool $isFile
     */
    public static function script($script, $isFile = 0)
    {
        if($isFile === 0){
            self::$script['script'][] = $script;
        }else{
            if($isFile === 2){
                self::$script['use'][] = $script;
            }else{
                self::$script['file'][] = $script;
            }
        }
    }

    /**
     * @title getScript
     * @description
     * @createtime 2019/2/25 下午10:53
     */
    public static function getScript()
    {
        return self::$script;
    }

    /**
     * @title style
     * @description
     * @createtime 2019/2/25 下午10:52
     * @param $style
     * @param int $isFile
     */
    public static function style($style, $isFile = 0)
    {
        if($isFile === 0){
            self::$style['style'][] = $style;
        }else{
            self::$style['file'][] = $style;
        }
    }

    /**
     * @title getStyle
     * @description
     * @createtime 2019/2/25 下午10:53
     */
    public static function getStyle()
    {
        return self::$style;
    }
}