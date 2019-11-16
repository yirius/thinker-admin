<?php


namespace Yirius\Admin\route\controller;


use think\captcha\Captcha;
use think\facade\Cache;
use think\facade\Response;
use think\facade\Validate;
use think\Request;
use Yirius\Admin\auth\AuthUser;
use Yirius\Admin\extend\ThinkerController;
use Yirius\Admin\table\ThinkerTable;
use Yirius\Admin\ThinkerAdmin;

class Admin extends ThinkerController
{
    protected $tokenAuth = [
        'except' => ['captcha', 'login', 'menus']
    ];

    /**
     * @title      captcha
     * @description
     * @createtime 2019/11/12 11:45 下午
     * @return \think\Response
     * @author     yangyuance
     */
    public function captcha()
    {
        return (new Captcha([
            // 验证码字体大小
            'fontSize'    =>    30,
            // 验证码位数
            'length'      =>    4,
            // 关闭验证码杂点
            'useNoise'    =>    true,
            'height'      =>    38
        ]))->entry();
    }

    /**
     * @title            login
     * @description 后台登录界面
     * @createtime       2019/11/12 7:29 下午
     * @param Request $request
     * @author           yangyuance
     */
    public function login(Request $request)
    {
        //验证信息
        $params = ThinkerAdmin::Validate()->make($request->param(), [
            'username' => "require",
            'password' => "require",
            'vercode'  => "require",
        ], [
            'username.require' => "请您填写用户名",
            'password.require' => "请您填写密码",
            'vercode.require'  => "请您填写动态验证码",
        ]);

        //判断当前使用的用户类型
        $accessType = intval($request->param("access_type", 0));

        //初始化Send
        $send = ThinkerAdmin::Send();

        //判断是否开启了验证码
        if(config('thinkeradmin.auth.vercode')){
            if(!captcha_check($request->param('vercode'))){
                $send->json([], 0, "验证码不正确");
            }
        }

        //判断是否已经超过了错误次数限制
        $loginErrorCount = config("thinkeradmin.auth.login_error_count");
        $loginCacheName = "login_count_" . addslashes($params['username']) . "_" . $accessType;
        if(!empty($loginErrorCount)){
            //找到当前登录用户名的次数
            $loginCount = Cache::get($loginCacheName, 0);
            if($loginCount > $loginErrorCount){
                $send->json([], 0, "登录密码错误次数超过限制，请您联系管理员");
            }
        }

        //前面都过了，就可以开始获取用户信息
        $auth = ThinkerAdmin::Auth()->setAccessType($accessType);

        //找到用户信息
        $userInfo = $auth->getUser(addslashes($params['username']));

        //如果不存在用户
        if(empty($userInfo)){
            $send->json([], 0, "用户名或密码错误");
        }

        //判断能否登录
        if(isset($userInfo['status']) && $userInfo['status'] == 0){
            $send->json([], 0, "用户无法登录，请您联系管理员");
        }

        //有一个状态参数
        $resultData = null;

        if($accessType === 0){
            //总后台登录,使用自定义的算法
            if ($userInfo['password'] != sha1($params['password'].$userInfo['salt'])) {
                $resultData = false;
            }else{
                $resultData = [
                    'id' => $userInfo['id'],
                    'username' => $userInfo['username'],
                    'access_type' => $accessType
                ];
            }
        }else{
            //判断是否存在自定义登录方法
            $login_verfiy_func = config("thinkeradmin.auth.login_verfiy_func");
            if($login_verfiy_func instanceof \Closure){
                $resultData = call($login_verfiy_func, [$params, $userInfo, $accessType]);
            }else{
                if ($userInfo['password'] != sha1($params['password'].$userInfo['salt'])) {
                    $resultData = false;
                }else{
                    $resultData = [
                        'id' => $userInfo['id'],
                        'username' => $userInfo['username'],
                        'access_type' => $accessType
                    ];
                }
            }
        }

        //如果是真等于false，说明失败了
        if($resultData === false){
            if(!empty($loginErrorCount)){
                //是否开启登录次数
                if(Cache::has($loginCacheName)){
                    Cache::inc($loginCacheName);
                }else{
                    Cache::set($loginCacheName, 1);
                }
            }
            $send->json([], 0, lang("incorrect username or password"));
        }else{
            if(!empty($loginErrorCount)) {
                //是否开启登录次数
                Cache::set($loginCacheName, 0);
            }

            //首先赋值token
            $resultData[config('thinkeradmin.auth.token_name')] = ThinkerAdmin::jwt()->encode($resultData);

            //否则的话直接返回对应的jwt
            $send->json($resultData, 1, lang("login success"));
        }
    }

    /**
     * @title      menus
     * @description 获取到菜单
     * @createtime 2019/11/13 2:17 下午
     * @author     yangyuance
     */
    public function menus()
    {
        $this->getAuth();

        //获取所有的菜单
        $menus = $this->auth->getMenus($this->tokenInfo['id']);

        //如果存在自定义菜单过滤，就使用
        $menu_fliter = config("thinkeradmin.menu_fliter");
        if($menu_fliter instanceof \Closure){
            $menus = call_user_func($menu_fliter, $menus, $this->tokenInfo, $this->auth);
        }

        ThinkerAdmin::Send()->json($menus);
    }

    /**
     * @title      rules
     * @description
     * @createtime 2019/11/15 6:47 下午
     * @author     yangyuance
     */
    public function rules()
    {
        ThinkerAdmin::Table(function(ThinkerTable $table){
            $table
                ->restful("/restful/thinkeradmin/TeAdminRules")
                ->setOperateUrl("thinkeradmin/Admin/rulesEdit");

            $table->columns()->setType("checkbox");

            $table->columns("id", "规则编号")->setWidth(80);

            $table->columns("pid", "上级编号")->setWidth(80);

            $table->columns("title", "规则名称")->setMinWidth(120);

            $table->columns("status", "状态")->switchs("status")->setWidth(80);

            $table->columns("type", "类型")->setWidth(80);

            $table->columns("list_order", "排序(大在前)")->setSort(true)->setWidth(80);

            $table->columns("url", "对应网址")->setMinWidth(120);

            $table->columns("op", "操作")->edit()->delete()->setWidth(150);

            $table->toolbar()->add()->delete()->event()->add()->delete();

            $table->colsEvent()->edit()->delete();

            $table->setLimit(1000);
        })->send("规则管理");
    }

    public function rulesEdit($id = 0)
    {

    }
}