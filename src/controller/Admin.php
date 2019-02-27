<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 下午4:18
 */

namespace Yirius\Admin\controller;


use Yirius\Admin\form\Form;
use Yirius\Admin\form\Inline;

class Admin
{
    public function index()
    {
        return \Yirius\Admin\Admin::form("testForm", function (Form $form) {

            $form->breadcrumb([
                ['text' => "测试主页"]
            ]);

            $form->button("test1", "测试1")
                ->danger()
                ->radius()
                ->xs()
                ->on("click", "layer.msg('111');");

            $form->text("test2", "测试文字");

            //inline
            $form->inline(function (Inline $formObj) {

                $formObj->text("test3", "测试inline文字1")->setAttributes('required', 'true');

                $formObj->text("test4", "测试inline文字2");

                $formObj->password("test5", "测试inline密码2");
            });

            $form->select("test6", "测试下拉")->setPlaceholder("测试")->options([
                ['text' => "第一个选项", 'value' => 1]
            ]);

            $form->date("test7", "测试时间");

            $form->date("test8", "测试时间1")
                ->setValue('2019-01-01 / 2019-01-02')
                ->range()
                ->onChange('console.log(value);');

            $form->radio("test9", "测试单选")->options([
                ['text' => "测试1", 'value' => "1"],
                ['text' => "测试2", 'value' => "2"],
                ['text' => "测试3", 'value' => "3"]
            ]);

            $form->checkbox("test10", "测试多选")->options([
                ['text' => "测试1", 'value' => "1"],
                ['text' => "测试2", 'value' => "2"],
                ['text' => "测试3", 'value' => "3"]
            ]);

            $form->checkbox("test11", "测试多选1")->options([
                ['text' => "测试1", 'value' => "1"],
                ['text' => "测试2", 'value' => "2"],
                ['text' => "测试3", 'value' => "3"]
            ])->primary();

            $form->switchs("test12", "测试开关");

            $form->textarea("test13", "测试文字area");

            $form->selectplus("test14", "测试下拉")->options([
                ['text' => "第一个选项", 'value' => 0],
                ['text' => "测试1", 'value' => "1"],
                ['text' => "测试2", 'value' => "2"],
                ['text' => "测试3", 'value' => "3"]
            ])->on('console.log(1);', false)->linkage('/thinkeradmin/test');

            $form->upload("test15", "测试上传");

            $form->footer()->submit("/thinkeradmin/test");

        })->show();
    }

    /**
     * @title config
     * @description get dynamic config
     * @createtime 2019/2/24 下午1:42
     * @param string $access_token
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function config($access_token = '')
    {
        if (empty($access_token)) {
            \Yirius\Admin\Admin::tools()->jsonSend([
                'menu' => []
            ]);
        } else {
            $member = \Yirius\Admin\Admin::jwt()->decode($access_token);

            \Yirius\Admin\Admin::tools()->jsonSend([
                'menu' => \Yirius\Admin\Admin::auth()->getAuthMenu($member['id'])
            ]);
        }
    }

    /**
     * @title login
     * @description
     * @createtime 2019/2/26 下午12:16
     * @param $username
     * @param $password
     * @param $vercode
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login($username, $password, $vercode)
    {
        //首先验证验证码输入
        if (!captcha_check($vercode)) {
            \Yirius\Admin\Admin::tools()->jsonSend([], 0, "验证码输入不正确, 请您重新输入");
        }
        //利用Auth来校验用户信息
        $userinfo = \Yirius\Admin\Admin::auth()->getUserinfo($username);
        //查无此用户
        if (empty($userinfo)) {
            \Yirius\Admin\Admin::tools()->jsonSend([], 0, "查无此用户");
        } else {
            //如果用户密码错误的话
            if ($userinfo['password'] != sha1($password . $userinfo['salt'])) {
                \Yirius\Admin\Admin::tools()->jsonSend([], 0, "用户登录账号密码错误");
            }
            //否则的话直接返回对应的jwt
            \Yirius\Admin\Admin::tools()->jsonSend([
                'id' => $userinfo['id'],
                'username' => $userinfo['username'],
                'userphone' => $userinfo['phone'],
                'config' => [
                    'menu' => \Yirius\Admin\Admin::auth()->getAuthMenu($userinfo['id'])
                ],
                'access_token' => \Yirius\Admin\Admin::jwt()->encode([
                    'id' => $userinfo['id'],
                    'type' => "admin",
                    'username' => $userinfo['username'],
                    'userphone' => $userinfo['phone'],
                ])
            ], 1, "登录成功");
        }
    }

    /**
     * @title logout
     * @description 退出登录
     * @createtime 2019/1/24 下午2:20
     */
    public function logout()
    {
        \Yirius\Admin\Admin::tools()->jsonSend([]);
    }

    /**
     * @title uploads
     * @description upload files or imgs
     * @createtime 2019/2/26 下午12:16
     * @param bool $isimage
     */
    public function uploads($isimage = true)
    {
        if($isimage){
            (new Upload())->images();
        }else{
            (new Upload())->upload();
        }
    }
}