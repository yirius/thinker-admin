<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 下午4:18
 */

namespace Yirius\Admin\controller;


use think\facade\Cache;
use think\Request;
use Yirius\Admin\extend\Upload;
use Yirius\Admin\form\Form;
use Yirius\Admin\table\Table;

class Admin
{
    /**
     * @title config
     * @description
     * @createtime 2019/6/12 2:27 PM
     * @param int $access_type
     * @param string $access_token
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function config($access_type = 0, $access_token = '')
    {
        if (empty($access_token)) {
            \Yirius\Admin\Admin::tools()->jsonSend([
                'menu' => []
            ]);
        } else {
            $member = \Yirius\Admin\Admin::jwt()->decode($access_token);

            //判断是否存在自定义登录方法
            $config_menu_func = config("thinkeradmin.auth.config_menu_func");
            //存在config获取方法，同时不是超级管理员
            if($config_menu_func instanceof \Closure && $access_type != 0){
                $menu = call($config_menu_func, [$member, $access_type]);
            }else{
                $menu = \Yirius\Admin\Admin::auth()->setAccessType($access_type)->getAuthMenu($member['id']);
            }
            \Yirius\Admin\Admin::tools()->jsonSend([
                'menu' => $menu
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
    public function login($username, $password, $vercode, $access_type = 0, $showpass = 0)
    {
        //new static
        $tools = \Yirius\Admin\Admin::tools();
        $auth = \Yirius\Admin\Admin::auth()->setAccessType($access_type);

        //首先验证验证码输入
        if (!captcha_check($vercode) && $showpass) {
            $tools->jsonSend([], 0, lang("incorrect verfiy"));
        }

        //判断是否存在登陆错误次数过多无法登录
        $loginErrorCount = config("thinkeradmin.auth.login_error_count");
        if(!empty($loginErrorCount)){
            //判断是否无法登陆了
            $login_count = Cache::get("login_count_" . $username . "_" . $access_type, 0);

            if($login_count > $loginErrorCount){
                $tools->jsonSend([], 0, "登录密码错误次数超过限制，请您联系管理员");
            }
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
                //状态没打开
                if($userinfo['status'] == 0){
                    $tools->jsonSend([], 0, "出现了未知问题，您无法登录，请您联系客服");
                }
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
                    $resultData = call($login_verfiy_func, [$username, $password, $userinfo, $access_type]);
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
                if(!empty($loginErrorCount)){
                    //是否开启登录次数
                    if(Cache::has("login_count_" . $username . "_" . $access_type)){
                        Cache::inc("login_count_" . $username . "_" . $access_type);
                    }else{
                        Cache::set("login_count_" . $username . "_" . $access_type, 1);
                    }
                }
                $tools->jsonSend([], 0, lang("incorrect username or password"));
            }else{
                if(!empty($loginErrorCount)) {
                    //是否开启登录次数
                    Cache::set("login_count_" . $username . "_" . $access_type, 0);
                }
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
     * @title theme
     * @description
     * @createtime 2019/3/4 下午5:44
     * @return \think\Response
     */
    public function theme()
    {
        return response(<<<HTML
<!-- 主题设置模板 -->
<script type="text/html" template>
    {{#
        var theme = layui.session.get("theme") || {},
        themeIndex =  parseInt((theme && theme.color) ? theme.color.index : 0) || 0;
    }}
    <div class="layui-card-header">
        配色方案
    </div>
    <div class="layui-card-body thinkeradmin-setTheme">
        <ul class="thinkeradmin-setTheme-color">
            {{# layui.each(layui.thinkeradmin.theme.color, function(index, item){ }}
            <li thinkeradmin-event="setTheme" 
                data-index="{{ index }}" 
                data-alias="{{ item.alias }}"
                {{ index === themeIndex ? 'class="layui-this"' : '' }} 
                title="{{ item.alias }}"
            >
                <div class="thinkeradmin-setTheme-header" style="background-color: {{ item.header }};"></div>
                <div class="thinkeradmin-setTheme-side" style="background-color: {{ item.main }};">
                    <div class="thinkeradmin-setTheme-logo" style="background-color: {{ item.logo }};"></div>
                </div>
            </li>
            {{# }); }}
        </ul>
    </div>
</script>
HTML
            , 200, [], 'html');
    }

    /**
     * @title editpwd
     * @description
     * @createtime 2019/3/21 下午1:28
     * @return mixed
     * @throws \Exception
     */
    public function editpwd()
    {
        return \Yirius\Admin\Admin::form("thinker_admin_editpwd", function(Form $form){

            $form->password("oldpassword", "旧密码");

            $form->password("password", "新密码");

            $form->password("repassword", "重复密码");

            $form->footer()->submit("/thinkeradmin/editpwdApi", 0, 'parent.layui.session.logout();', 'if((obj.field.password != obj.field.repassword) || obj.field.password == ""){ layui.tools.alert("两次输入密码不一致，或输入空密码");throw("两次输入密码不一致");} return obj;');

        })->show();
    }

    /**
     * @title editpwdApi
     * @description
     * @createtime 2019/3/21 下午2:14
     * @param Request $request
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function editpwdApi(Request $request)
    {
        //judge is there have access_token
        $access_token = $request->param('access_token');
        if(empty($access_token)){
            \Yirius\Admin\Admin::tools()->jsonSend([], 1001, lang("no access_token to access"));
        }
        //if success, save token info
        $tokenInfo = \Yirius\Admin\Admin::jwt()->decode($access_token, function($err){
            \Yirius\Admin\Admin::tools()->jsonSend([], 1001, lang("no authority to access"));
        });

        //find params
        $password = $request->param("password");
        $oldpassword = $request->param("oldpassword");
        //find userinfo
        $userModelTable = config("thinkeradmin.auth.auth_user")[$tokenInfo["access_type"]];
        $userModel = db($userModelTable);
        $userinfo = $userModel->where('id', '=', $tokenInfo['id'])->find();
        //edit pwd
        $login_update_func = config("thinkeradmin.auth.login_update_func");
        if($tokenInfo["access_type"] != 0 && $login_update_func instanceof \Closure){
            //edit other pwd
            call($login_update_func, [$password, $oldpassword, $userinfo, $userModelTable]);
        }else{
            //not have funcs, then edit with sha1
            if ($userinfo['password'] != sha1($oldpassword . $userinfo['salt'])) {
                \Yirius\Admin\Admin::tools()->jsonSend([], 0, lang("incorrect username or password"));
            }
            $userinfo['salt'] = \Yirius\Admin\Admin::tools()->rand();
            $userinfo['password'] = sha1($password . $userinfo['salt']);
            $flag = $userModel->where('id', '=', $userinfo['id'])->update($userinfo);
            if($flag){
                Cache::clear("thinker_admin_auth");
                \Yirius\Admin\Admin::tools()->jsonSend([], 1, lang("edit password success,redirecting..."));
            }else{
                \Yirius\Admin\Admin::tools()->jsonSend([], 0, lang("edit password error"));
            }
        }
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