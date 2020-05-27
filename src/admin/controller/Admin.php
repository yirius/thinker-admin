<?php


namespace Yirius\Admin\admin\controller;


use think\captcha\Captcha;
use think\Request;
use Yirius\Admin\admin\model\AdminLogsModel;
use Yirius\Admin\admin\model\AdminRulesModel;
use Yirius\Admin\config\ConsConfig;
use Yirius\Admin\extend\ThinkerController;
use Yirius\Admin\renders\ThinkerForm;
use Yirius\Admin\support\abstracts\LoginAbstract;
use Yirius\Admin\support\factory\LoginFactory;
use Yirius\Admin\ThinkerAdmin;

class Admin extends ThinkerController
{
    protected $tokenAuth = false;

    /**
     * @title      captcha
     * @description
     * @createtime 2020/5/28 12:14 上午
     * @return \think\Response
     * @author     yangyuance
     */
    public function captcha() {
        return (new Captcha(ThinkerAdmin::properties()->getCaptcha()))->entry();
    }


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

        //判断验证码
        if(!empty(ThinkerAdmin::properties()->getShiro("vercode"))) {
            if(!captcha_check($request->param('vercode'))){
                ThinkerAdmin::response()->msg("验证码不正确")->fail();
            }
        }

        /**
         * @var LoginAbstract
         */
        $loginService = (new LoginFactory())->loadLogin();

        try{
            $userToken = $loginService->login($params['username'], $params['password']);

            if(!isset($userToken[ConsConfig::$JWT_KEY]) || !isset($userToken[ConsConfig::$JWT_ACCESS_TYPE])) {
                ThinkerAdmin::response()->msg("不存在相关返回参数")->fail();
            }

            $userToken[ConsConfig::$JWT_HEADER] = ThinkerAdmin::jwt()->sign($userToken);

            //无论是否单点登录，都需要记录登陆的ip
            ThinkerAdmin::cache()->setTokenIp($userToken, $request->ip());

            //设置操作时间
            ThinkerAdmin::cache()->setTokenOperateTime($userToken);

            (new AdminLogsModel())->addLog($userToken, "用户登录", true);

            ThinkerAdmin::response()->data($userToken)->msg(lang("login success"))->success();
        }catch (\Exception $exception) {
            thinker_error($exception);
            ThinkerAdmin::response()->msg($exception->getMessage())->fail();
        }
    }

    /**
     * @title      menus
     * @description
     * @createtime 2020/5/28 12:44 上午
     * @author     yangyuance
     */
    public function menus() {
        $tokenInfo = ThinkerAdmin::jwt()->getTokenInfo();

        $menuTrees = ThinkerAdmin::cache()->getAuthCache("menus", $tokenInfo, null);

        if(empty($menuTrees)) {
            $rules = (new AdminRulesModel())->findUserRules($tokenInfo, [1]);

            $menuTrees = ThinkerAdmin::tree()->setItemEach(function ($value) {
                return [
                    'id' => $value['id'],
                    'pid' => $value['pid'],
                    'href' => $value['url'],
                    'icon' => $value['icon'],
                    'name' => $value['name'],
                    'title' => $value['title'],
                ];
            })->tree($rules);

            ThinkerAdmin::cache()->setAuthCache("menus", $tokenInfo, $menuTrees);
        }

        ThinkerAdmin::response()->data($menuTrees)->success();
    }

    /**
     * @title      upload
     * @description
     * @createtime 2020/5/28 12:46 上午
     * @param int $isImage
     * @author     yangyuance
     */
    public function upload($isImage = 1) {
        ThinkerAdmin::response()->data(
            ThinkerAdmin::upload()->upload($isImage)
        )->success();
    }

    /**
     * @title      clearcache
     * @description
     * @createtime 2020/5/28 12:47 上午
     * @param string $password
     * @author     yangyuance
     */
    public function clearcache($password = "") {
        $this->verifyPassword($password);

        ThinkerAdmin::cache()->clearAuthCache();

        ThinkerAdmin::response()->success();
    }

    /**
     * @title      userinfo
     * @description
     * @createtime 2020/5/28 12:56 上午
     * @param Request $request
     * @author     yangyuance
     */
    public function userinfo(Request $request) {
        if ($request->isPost()) {
            $tokenInfo = ThinkerAdmin::jwt()->getTokenInfo();

            /**
             * @var LoginAbstract
             */
            $loginAbstract = ThinkerAdmin::jwt()->getLoginFactory(
                $tokenInfo[ConsConfig::$JWT_ACCESS_TYPE]
            );

            $isUpdated = $loginAbstract->updatePassword(
                $request->param("oldpwd"),
                $request->param("password"),
                $tokenInfo[ConsConfig::$JWT_KEY]
            );

            if(!empty($isUpdated)) {
                ThinkerAdmin::response()->msg("密码更改完成")->success();
            } else {
                ThinkerAdmin::response()->msg("旧密码不正确，无法更改")->fail();
            }
        } else {
            ThinkerAdmin::form(function (ThinkerForm $thinkerForm) {
                $thinkerForm->password("oldpwd", "旧密码");

                $thinkerForm->password("password", "新密码");

                $thinkerForm->password("repassword", "重复新密码");

                $thinkerForm->switchs("theme", "明亮模式")
                    ->on("layui.admin.themeChange(obj.elem.checked)");

                ThinkerAdmin::script("if(layui.session.get('layoutTheme')){
                    $('#".$thinkerForm->getId()."_theme').next().click();
                }");

                ThinkerAdmin::script("md5", false, true);
             })->submit(
                 "/thinkeradmin/Admin/userinfo", null, null,
                "function(obj, url){" .
                "obj.field.oldpwd = layui.md5(obj.field.oldpwd);" .
                "obj.field.password = layui.md5(obj.field.password);" .
                "obj.field.repassword = layui.md5(obj.field.repassword);" .
                "return {data: obj.field};" .
                "}"
            )->send("个人设置");
        }
    }
}