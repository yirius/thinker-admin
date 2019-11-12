<?php


namespace Yirius\Admin;


use Yirius\Admin\auth\Auth;
use Yirius\Admin\auth\Jwt;
use Yirius\Admin\widgets\Send;
use Yirius\Admin\widgets\Tree;
use Yirius\Admin\widgets\Validate;

class ThinkerAdmin
{
    /**
     * @title      Send
     * @description 返回输出实例
     * @createtime 2019/11/12 7:25 下午
     * @return Send
     * @author     yangyuance
     */
    public static function Send()
    {
        return (new Send());
    }

    /**
     * @title      Validate
     * @description
     * @createtime 2019/11/12 7:29 下午
     * @return Validate
     * @author     yangyuance
     */
    public static function Validate()
    {
        return (new Validate());
    }

    /**
     * @title      Tree
     * @description
     * @createtime 2019/11/12 10:55 下午
     * @return Tree
     * @author     yangyuance
     */
    public static function Tree()
    {
        return (new Tree());
    }

    /**
     * @title      Tree
     * @description
     * @createtime 2019/11/12 10:55 下午
     * @return Auth
     * @author     yangyuance
     */
    public static function Auth()
    {
        return (new Auth());
    }

    /**
     * @title      Jwt
     * @description
     * @createtime 2019/11/12 11:30 下午
     * @return Jwt
     * @author     yangyuance
     */
    public static function Jwt()
    {
        return (new Jwt());
    }
}