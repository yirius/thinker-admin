<?php

namespace Yirius\Admin\extend;

use think\App;
use think\Controller;
use think\facade\Cache;
use think\Route;
use Yirius\Admin\auth\Auth;
use Yirius\Admin\table\ThinkerTable;
use Yirius\Admin\ThinkerAdmin;

class ThinkerController extends Controller
{
    /**
     * 需要进行token验证的数据
     * @var array
     */
    protected $tokenAuth = [];

    /**
     * @var array
     */
    protected $tokenInfo = [];

    /**
     * @var Auth
     */
    protected $auth = null;

    /**
     * @var string
     */
    protected $urlPath = '';

    /**
     * Table对应的name，方便查找edit/del等参数
     * @var string
     */
    protected $urlName = '';

    /**
     * @title      initialize
     * @description
     * @createtime 2019/11/12 7:11 下午
     * @author     yangyuance
     */
    protected function initialize()
    {
        $actionName = $this->request->action();
        //拼装当前访问的url
        $this->urlPath = "/".$this->request->module()."/".$this->request->controller()."/".$actionName;
        //如果不存在action，说明是通过router访问的
        if(empty($actionName)){
            $dispatch = $this->request->dispatch()->getDispatch();
            $actionName = $dispatch[count($dispatch)-1];
            //释放参数
            $dispatch = null;

            //说明是通过路由访问的，需要获取到路由规则
            $routeInfo = $this->request->routeInfo();
            //判断url是否有其他参数
            $this->urlPath = "/".$routeInfo['rule'];
            //因为restful最后会存在附加id，所以需要判断并去除
            if(in_array($actionName, ['read', 'update', 'delete']) &&
                strpos($this->urlPath, "/<id>") >= 0){
                $this->urlPath = str_replace("/<id>", "", $this->urlPath);
            }
            //判断是否有自定义参数
            if(strpos($this->urlPath, "/<") >= 0){
                foreach($routeInfo['var'] as $i => $item){
                    $this->urlPath = str_replace("<".$i.">", $item, $this->urlPath);
                }
            }
            //释放参数
            $routeInfo = null;
        }

        //如果存在only，就是只验证指定规则
        if(isset($this->tokenAuth['only'])){
            $only = array_map(function ($item) {
                return strtolower($item);
            }, $this->tokenAuth['only']);
        } else if(isset($this->tokenAuth['except'])){
            //如果存在except，就是这些排除
            $except = array_map(function ($item) {
                return strtolower($item);
            }, $this->tokenAuth['except']);
        }

        if (isset($only) && !in_array($actionName, $only)) {
            //只验证这些
            $this->checkUrlAuth();
        } elseif (isset($except) && in_array($actionName, $except)) {
            //在数组内的不验证，直接过
        } else {
            if(isset($this->tokenAuth['auth']) && empty($this->tokenAuth['auth'])){
                //除非强制设置为false，否则都验证
            }else{
                //其他情况都需要验证
                $this->checkUrlAuth();
            }
        }
    }

    /**
     * @title      getAuth
     * @description 获取到Auth验证信息
     * @createtime 2019/11/13 2:12 下午
     * @author     yangyuance
     */
    protected function getAuth()
    {
        if(is_null($this->auth)){
            $tokenName = config('thinkeradmin.auth.token_name');
            $headerToken = $this->request->header($tokenName, false);
            $paramToken = input('param.' . $tokenName, false);

            if($headerToken || $paramToken){
                //获取TokenInfo，然后判断一下如果是1002的返回码，就
                $this->tokenInfo = ThinkerAdmin::Jwt()
                    ->setExpiredCall(function($payload, $err){
                        $err = null;
                        $lastUseTime = ThinkerAdmin::Cache()->getTokenCache("jwt", [
                            'id' => $payload->payload->id,
                            'access_type' => $payload->payload->access_type
                        ], 0);

                        if(time() - $lastUseTime <= config("thinkeradmin.jwt.expired_operate_time")){
                            //如果还没超过操作时间
                            $lastUseTime = null;
                            $tokenData = [];
                            foreach($payload->payload as $key => $item){
                                $tokenData[$key] = $item;
                            }

                            //重新序列化参数
                            $tokenData[config('thinkeradmin.auth.token_name')] =
                                ThinkerAdmin::jwt()->encode($tokenData);

                            //发送重新注册token的参数
                            ThinkerAdmin::Send()->json(
                                $tokenData,
                                config("thinkeradmin.jwt.reexpired_code"),
                                lang("authorization has expired")
                            );
                        } else {
                            $lastUseTime = null;
                            ThinkerAdmin::Send()->json(
                                [],
                                config("thinkeradmin.jwt.expired_code"),
                                lang("authorization has expired")
                            );
                        }
                    })
                    ->decode($headerToken ? $headerToken : $paramToken);

                //判断登录的ip是否一致
                if(config('thinkeradmin.auth.singleLogin')){
                    $recordIp = ThinkerAdmin::Cache()
                        ->getAuthCache("loginip", $this->tokenInfo, "");
                    if(empty($recordIp)){
                        ThinkerAdmin::Cache()
                            ->setAuthCache("loginip", $this->tokenInfo, $this->request->ip());
                    }else{
                        if($this->request->ip() != $recordIp){
                            ThinkerAdmin::Send()->json([], 0, "该账号存在他人登录，您已被强制下线");
                        }
                    }
                }

                //设置缓存判断当前的token状态, 缓存设置的过期时间
                ThinkerAdmin::Cache()->setTokenCache(
                    "jwt",
                    $this->tokenInfo,
                    time(),
                    config("thinkeradmin.jwt.expired_operate_time")
                );

                $this->auth = ThinkerAdmin::Auth()->setAccessType($this->tokenInfo['access_type']);
            }else{
                ThinkerAdmin::Send()->json([], 0, "不存在Auth信息，无法验证身份");
            }
        }
    }

    /**
     * @title      checkAuth
     * @description 检查权限
     * @createtime 2019/11/12 7:12 下午
     * @param $actionName
     * @author     yangyuance
     */
    protected function checkUrlAuth()
    {
        //首先获取Auth信息
        $this->getAuth();

        $this->urlName = $this->auth->checkUrl($this->urlPath, $this->tokenInfo['id'], [1,2]);

        if($this->urlName === false){
            ThinkerAdmin::Send()->json([], 0, "Auth信息失败: 您暂无权限访问");
        }
    }

    /**
     * @title      checkLoginPwd
     * @description
     * @createtime 2019/11/16 8:44 下午
     * @param bool $password
     * @author     yangyuance
     */
    protected function checkLoginPwd($password = false)
    {
        //首先获取Auth信息
        $this->getAuth();

        $password = $this->request->param("password", $password);
        if(empty($password)) {
            ThinkerAdmin::Send()->json([], 0, lang("empty password"));
        }

        //获取用户信息
        $userInfo = $this->auth->getUser($this->tokenInfo['id'], "id");

        //如果不存在用户
        if(empty($userInfo)){
            ThinkerAdmin::Send()->json([], 0, "用户名或密码错误");
        }

        //判断能否登录
        if(isset($userInfo['status']) && $userInfo['status'] == 0){
            ThinkerAdmin::Send()->json([], 0, "用户无法登录使用，请您联系管理员");
        }


        //有一个状态参数
        $resultData = true;

        if($this->tokenInfo['access_type'] === 0){
            //总后台登录,使用自定义的算法
            if ($userInfo['password'] != sha1($password.$userInfo['salt'])) {
                $resultData = false;
            }
        }else{
            //判断是否存在自定义登录方法
            $login_verfiy_func = config("thinkeradmin.auth.login_verfiy_func");
            if($login_verfiy_func instanceof \Closure){
                $resultData = call($login_verfiy_func, [
                    ['password' => $password], $userInfo, $this->tokenInfo['access_type']
                ]);
            }else{
                if ($userInfo['password'] != sha1($password.$userInfo['salt'])) {
                    $resultData = false;
                }
            }
        }

        if($resultData === false){
            ThinkerAdmin::Send()->json([], 0, lang("incorrect username or password"));
        }
    }

    /**
     * @title      renderTableRule
     * @description
     * @createtime 2019/11/28 3:43 下午
     * @param ThinkerTable $table
     * @param string       $title
     * @return \Yirius\Admin\table\ThinkerTableCols
     * @author     yangyuance
     */
    protected function renderTableRule(ThinkerTable $table, $title = "操作")
    {
        //按钮是否存在
        $isShowAdd = $this->auth->checkRule($this->urlName.":add", $this->tokenInfo['id'], [3]);
        $isShowDelete = $this->auth->checkRule($this->urlName.":del", $this->tokenInfo['id'], [3]);
        $isShowEdit = $this->auth->checkRule($this->urlName.":edit", $this->tokenInfo['id'], [3]);

        //实例化操作栏
        $opColumn = $table->columns("op", $title);

        //判断后方按钮
        if($isShowEdit && $isShowDelete){
            $opColumn->edit()->delete()->setWidth(145);
            $table->colsEvent()->edit()->delete();
        }elseif($isShowEdit){
            $opColumn->edit()->setWidth(81);
            $table->colsEvent()->edit();
        }elseif($isShowDelete){
            $opColumn->delete()->setWidth(81);
            $table->colsEvent()->delete();
        }

        //判断上方按钮
        if($isShowAdd && $isShowDelete){
            $table->toolbar()->add()->delete()->event()->add()->delete();
        }elseif($isShowAdd){
            $table->toolbar()->add()->event()->add();
        }elseif($isShowDelete){
            $table->toolbar()->delete()->event()->delete();
        }

        return $opColumn;
    }
}