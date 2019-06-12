<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/26
 * Time: 下午12:21
 */

namespace Yirius\Admin\controller;


use think\Controller;
use think\facade\Env;
use think\facade\Route;

class AdminController extends Controller
{
    /**
     * @var array
     */
    protected $token = [];

    /**
     * @var bool
     */
    protected $returnJsonError = false;

    /**
     * which access type to check
     * @var int
     */
    protected $accessType = 0;

    /**
     * which rule type to check
     * @var int
     */
    protected $checkType = 1;

    /**
     * @title initialize
     * @description
     * @createtime 2019/3/3 下午7:46
     * @throws \Exception
     */
    protected function initialize()
    {
        //judge is there have access_token
        $access_token = $this->request->param('access_token');
        if(empty($access_token)){
            $this->sendError(lang("no access_token to access"));
        }
        //if success, save token info
        $this->token = \Yirius\Admin\Admin::jwt()->decode($access_token, function($err){
            $this->sendError(lang("no authority to access"));
        });

        //get path, check resource restful url
        $currentPath = $this->getCurrentPath();
        //if check error, sendError
        if(!\Yirius\Admin\Admin::auth()
            ->setAccessType($this->accessType)
            ->check($currentPath, $this->getToken('id'), $this->checkType)
        ){
            $this->sendError(lang("do not have authority to access", ['url' => $currentPath]), 0);
        }
    }

    /**
     * @title getCurrentPath
     * @description get this request's path
     * @createtime 2019/2/27 下午3:44
     */
    protected function getCurrentPath()
    {
        $currentPath = $this->request->path();
        //if there no prev fix '/', make it
        if(substr($currentPath, 0, 1) != "/") $currentPath = "/" . $currentPath;

        //check resource like
        $paths = explode("/", $currentPath);
        foreach($paths as $i => $v){
            if(is_numeric($v) && $i == (count($paths) - 1)){
                unset($paths[$i]);
            }
        }

        Env::set("THINKER_PATH", join("/", $paths));

        return join("/", $paths);
    }

    /**
     * @title getToken
     * @description
     * @createtime 2019/2/26 下午3:11
     * @param null $field
     * @return array|bool|mixed
     */
    protected function getToken($field = null)
    {
        if(is_null($field)){
            return $this->token;
        }else{
            if(!isset($this->token[$field])){
                return false;
            }else{
                return $this->token[$field];
            }
        }
    }

    /**
     * @title sendError
     * @description
     * @createtime 2019/2/26 下午3:06
     * @param $msg
     * @param int $code
     */
    protected function sendError($msg, $code = 1001)
    {
        if($this->returnJsonError){
            \Yirius\Admin\Admin::tools()->jsonSend([], $code, $msg);
        }else{
            $logout = $code == 1001 ? 'layui.session.logout();' : '';
            response(<<<HTML
<script>
parent.layui.layer.alert("{$msg}", {title: "温馨提示"}, function(index){
    layer.closeAll();
    {$logout}
});
</script>
HTML
            )->send();
            exit;
        }
    }

    /**
     * @title checkLoginPwd
     * @description
     * @createtime 2019/3/4 上午1:22
     */
    protected function checkLoginPwd()
    {
        $password = $this->request->param("password");
        if(empty($password))
        {
            \Yirius\Admin\Admin::tools()->jsonSend([], 0, lang("empty password"));
        }

        $userinfo = \Yirius\Admin\Admin::auth()
            ->setAccessType($this->getToken("access_type"))
            ->getUserinfo($this->getToken("id"), "id");

        $resultData = true;
        //判断是否存在自定义登录方法
        if($this->getToken("access_token") == 0){
            //如果用户密码错误的话
            if ($userinfo['password'] != sha1($password . $userinfo['salt'])) {
                $resultData = false;
            }
        }else{
            $login_verfiy_func = config("thinkeradmin.auth.login_verfiy_func");
            if($login_verfiy_func instanceof \Closure){
                $resultData = call($login_verfiy_func, [
                    $this->getToken("username"),
                    $password,
                    $userinfo,
                    $this->getToken("access_type")
                ]);
            }else{
                //如果用户密码错误的话
                if ($userinfo['password'] != sha1($password . $userinfo['salt'])) {
                    $resultData = false;
                }
            }
        }
        if($resultData === false){
            \Yirius\Admin\Admin::tools()->jsonSend([], 0, lang("incorrect username or password"));
        }
    }

    /**
     * @title checkAuth
     * @description
     * @createtime 2019/3/7 下午9:26
     * @param $rule
     * @param int $type
     * @return bool
     * @throws \Exception
     */
    protected function checkAuth($rule, $type = 1)
    {
        return \Yirius\Admin\Admin::auth()
            ->setAccessType($this->getToken("access_type"))
            ->check($rule, $this->getToken("id"), $type);
    }
}