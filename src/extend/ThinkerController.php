<?php

namespace Yirius\Admin\extend;

use think\App;
use think\Controller;

class ThinkerController extends Controller
{
    /**
     * 需要进行token验证的数据
     * @var array
     */
    protected $tokenAuth = [];

    /**
     * @title      initialize
     * @description
     * @createtime 2019/11/12 7:11 下午
     * @author     yangyuance
     */
    protected function initialize()
    {
        $actionName = $this->request->action();
        if(empty($actionName)){
            $dispatch = $this->request->dispatch()->getDispatch();
            $actionName = $dispatch[count($dispatch)-1];
        }

        $needAuth = true;
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
        } elseif (isset($except) && in_array($actionName, $except)) {
            //在数组内的不验证，直接过
        } else {
            //其他情况都需要验证
        }
    }

    /**
     * @title      checkAuth
     * @description 检查权限
     * @createtime 2019/11/12 7:12 下午
     * @param $actionName
     * @author     yangyuance
     */
    protected function checkAuth($actionName)
    {

    }
}