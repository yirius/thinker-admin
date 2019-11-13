<?php

namespace Yirius\Admin\extend;

use think\App;
use think\Controller;
use Yirius\Admin\auth\Auth;
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

        //判断那些是需要获取auth
        if(isset($this->tokenAuth['nocheck'])){
            //如果存在except，就是这些排除
            $nocheck = array_map(function ($item) {
                return strtolower($item);
            }, $this->tokenAuth['nocheck']);
        }

        if (isset($only) && !in_array($actionName, $only)) {
            //只验证这些
            $this->checkUrlAuth();
        } elseif (isset($except) && in_array($actionName, $except)) {
            //在数组内的不验证，直接过
        } else {
            //其他情况都需要验证
            $this->checkUrlAuth();
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
                $this->tokenInfo = ThinkerAdmin::Jwt()->decode($headerToken ? $headerToken : $paramToken);
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

        if(!$this->auth->checkUrl($this->urlPath, $this->tokenInfo['id'])){
            ThinkerAdmin::Send()->json([], 0, "Auth信息失败: 您暂无权限访问");
        }
    }
}