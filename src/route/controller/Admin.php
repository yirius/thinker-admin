<?php


namespace Yirius\Admin\route\controller;


use think\captcha\Captcha;
use think\facade\Cache;
use think\Request;
use Yirius\Admin\extend\ThinkerController;
use Yirius\Admin\form\ThinkerForm;
use Yirius\Admin\services\ThinkerUpload;
use Yirius\Admin\ThinkerAdmin;

class Admin extends ThinkerController
{
    /**
     * @var array
     */
    protected $tokenAuth = [
        'auth' => false
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

        $configUserName = config("thinkeradmin.auth.username");
        
        if($accessType === 0){
            //总后台登录,使用自定义的算法
            if ($userInfo['password'] != sha1($params['password'].$userInfo['salt'])) {
                $resultData = false;
            }else{
                $resultData = [
                    'id' => $userInfo['id'],
                    'username' => isset($configUserName[$accessType]) ?
                        $userInfo[$configUserName[$accessType]] : $userInfo['username'],
                    'access_type' => $accessType,
                    'theme' => isset($userInfo['theme']) ? $userInfo['theme'] : ""
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
                        'username' => isset($configUserName[$accessType]) ?
                            $userInfo[$configUserName[$accessType]] : $userInfo['username'],
                        'access_type' => $accessType,
                        'theme' => isset($userInfo['theme']) ? $userInfo['theme'] : ""
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

            //记录本次登录的ip地址
            if(config('thinkeradmin.auth.singleLogin')){
                ThinkerAdmin::Cache()->setAuthCache("loginip", $resultData, $this->request->ip());
            }

            thinker_log($resultData, "用户登录", true);

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
        if(is_callable($menu_fliter)){
            $menus = call($menu_fliter, [$menus, $this->tokenInfo, $this->auth]);
        }

        ThinkerAdmin::Send()->json($menus);
    }

    /**
     * @title      clearcache
     * @description 清理侧边栏缓存等信息
     * @createtime 2019/11/25 9:12 下午
     * @author     yangyuance
     */
    public function clearcache()
    {
        $this->checkLoginPwd();

        ThinkerAdmin::Cache()->clearAuthCache();

        ThinkerAdmin::Send()->json();
    }

    /**
     * @title      userinfo
     * @description 用户信息
     * @createtime 2019/11/25 9:20 下午
     * @author     yangyuance
     */
    public function userinfo()
    {
        $this->getAuth();

        /**
         * 获取到提交的信息
         */
        if($this->request->isPost()){
            $params = ThinkerAdmin::Validate()->make(input('param.'), [
                'realname' => "require",
                'oldpassword' => "require",
                'password' => "require|length:6",
                'repassword' => "require"
            ], [
                'realname.require' => "展示姓名必须填写",
                'oldpassword.require' => "旧密码必须填写",
                'password.require' => "新密码必须填写",
                'password.length' => "新密码必须填写超过6位",
                'repassword.require' => "重复新密码必须填写"
            ]);

            $userInfo = $this->auth->getUser($this->tokenInfo['id'], "id");

            if($params['password'] == $params['repassword']){
                if(sha1($params['oldpassword'].$userInfo['salt']) == $userInfo['password']){
                    $salt = ThinkerAdmin::Tools()->rand();
                    $flag = db($this->auth->getConfig("auth_user")[$this->auth->getAccessType()])
                        ->where('id', '=', $this->tokenInfo['id'])
                        ->update([
                            'salt' => $salt,
                            'password' => sha1($params['password'] . $salt),
                            'realname' => $params['realname'],
                            'theme' => input('param.theme', '')
                        ]);

                    if($flag){
                        ThinkerAdmin::Cache()->clearAuthCache();

                        thinker_log([
                            'id' => $this->tokenInfo['id'],
                            'username' => $params['realname'],
                            'access_type' => $this->tokenInfo['access_type']
                        ], "修改个人资料");

                        ThinkerAdmin::Send()->json([], 1, "修改个人信息成功");
                    }else{
                        ThinkerAdmin::Send()->json([], 0, "修改失败");
                    }
                }else{
                    ThinkerAdmin::Send()->json([], 0, "旧密码输入不正确");
                }
            }else{
                ThinkerAdmin::Send()->json([], 0, "重复密码输入不正确");
            }
        }

        ThinkerAdmin::Form(function(ThinkerForm $form){

            $userInfo = $this->auth->getUser($this->tokenInfo['id'], "id");

            $form->text("realname", "展示姓名")
                ->setValue(isset($userInfo['realname']) ? $userInfo['realname'] : "");

            $form->password("oldpassword", "旧密码");

            $form->password("password", "新密码");

            $form->password("repassword", "重复密码");

            $form->select("theme", "侧边栏颜色")->options([
                ['text' => "深色模式", 'value' => ""],
                ['text' => "浅色模式", 'value' => "thinker-theme-white"],
            ])->on(<<<HTML
layui.admin.session.set("theme_class", obj.value);
layui.$("#"+layui.conf.views.sidebar).attr("class", 'layui-side '+obj.value);
HTML
            )->setPlaceholder()->setValue(isset($userInfo['theme']) ? $userInfo['theme'] : "");

        })->submit("/thinkeradmin/Admin/userinfo")->send("个人信息");
    }

    /**
     * @title      upload
     * @description
     * @createtime 2019/11/25 11:13 下午
     * @author     yangyuance
     */
    public function upload($isimage = true)
    {
        ThinkerAdmin::Send()->json(
            (new ThinkerUpload())->upload(true, $isimage)
        );
    }
}