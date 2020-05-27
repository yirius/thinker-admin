<?php


namespace Yirius\Admin\support\abstracts;


abstract class LoginAbstract
{

    /**
     * @title      getUser
     * @description 获取到用户信息
     * @createtime 2020/5/27 1:00 上午
     * @param $id
     * @return array
     * @author     yangyuance
     */
    public abstract function getUser($id);

    /**
     * @title      login
     * @description 登陆的接口
     * @createtime 2020/5/27 12:59 上午
     * @param $username
     * @param $password
     * @return array
     * @author     yangyuance
     */
    public abstract function login($username, $password);

    /**
     * @title      verifyPassword
     * @description 校验密码
     * @createtime 2020/5/27 12:59 上午
     * @param $password
     * @param $user
     * @return boolean
     * @author     yangyuance
     */
    public abstract function verifyPassword($password, $user);

    /**
     * @title      updatePassword
     * @description 更新用户密码
     * @createtime 2020/5/27 12:59 上午
     * @param $oldPassword
     * @param $newPassword
     * @param $id
     * @return boolean
     * @author     yangyuance
     */
    public abstract function updatePassword($oldPassword, $newPassword, $id);
}