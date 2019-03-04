<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 下午4:18
 */

namespace Yirius\Admin\controller;


use Yirius\Admin\extend\Upload;
use Yirius\Admin\form\Form;
use Yirius\Admin\form\Inline;
use Yirius\Admin\model\table\AdminRule;

class Admin
{
    public function index()
    {
        return \Yirius\Admin\Admin::form("testForm", function (Form $form) {

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

            $form->checkbox("test10[]", "测试多选")->options([
                ['text' => "测试1", 'value' => "1"],
                ['text' => "测试2", 'value' => "2"],
                ['text' => "测试3", 'value' => "3"]
            ]);

            $form->checkbox("test11[]", "测试多选1")->options([
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
            ])->on('console.log(1);', false);

            $form->upload("test15", "测试上传");

            $form->wangeditor("test16", "富文本");

            $form->tree("test17", "书文本")->setData(AdminRule::adminSelect()->getResult());

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
     * @createtime 2019/3/3 下午7:18
     * @param $username
     * @param $password
     * @param $vercode
     * @param int $access_type
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login($username, $password, $vercode, $access_type = 0)
    {
        //new static
        $tools = \Yirius\Admin\Admin::tools();
        $auth = \Yirius\Admin\Admin::auth()->setAccessType($access_type);

        //首先验证验证码输入
        if (!captcha_check($vercode)) {
            $tools->jsonSend([], 0, lang("incorrect verfiy"));
        }
        //利用Auth来校验用户信息
        $userinfo = $auth->getUserinfo($username);
        //查无此用户
        if (empty($userinfo)) {
            $tools->jsonSend([], 0, lang("empty userinfo"));
        } else {
            //返回的数据
            $resultData = [];
            //判断是否是总后台登录
            if($access_type === 0){
                //如果用户密码错误的话
                if ($userinfo['password'] != sha1($password . $userinfo['salt'])) {
                    $resultData = false;
                }else{
                    $resultData = [
                        'id' => $userinfo['id'],
                        'username' => $userinfo['username'],
                        'userphone' => $userinfo['phone'],
                        'type' => "admin",
                        'access_type' => $access_type
                    ];
                }
            }else{
                //判断是否存在自定义登录方法
                $login_verfiy_func = config("thinkeradmin.auth.login_verfiy_func");
                if($login_verfiy_func instanceof \Closure){
                    $resultData = call($login_verfiy_func, [$username, $password, $userinfo]);
                }else{
                    //如果用户密码错误的话
                    if ($userinfo['password'] != sha1($password . $userinfo['salt'])) {
                        $resultData = false;
                    }else{
                        $resultData = [
                            'id' => $userinfo['id'],
                            'username' => $userinfo['username'],
                            'userphone' => $userinfo['phone'],
                            'type' => "user",
                            'access_type' => $access_type
                        ];
                    }
                }
            }
            //如果是真等于false，说明失败了
            if($resultData === false){
                $tools->jsonSend([], 0, lang("incorrect username or password"));
            }else{
                //首先赋值token
                $resultData['access_token'] = \Yirius\Admin\Admin::jwt()->encode($resultData);
                //然后赋值config
                $resultData['config'] = [
                    'menu' => $auth->getAuthMenu($userinfo['id'])
                ];
                //否则的话直接返回对应的jwt
                $tools->jsonSend($resultData, 1, lang("login success"));
            }
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
        (new Upload())->upload(false, $isimage);
    }
}