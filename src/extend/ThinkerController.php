<?php


namespace Yirius\Admin\extend;


use think\Controller;
use Yirius\Admin\admin\model\AdminRulesModel;
use Yirius\Admin\config\ConsConfig;
use Yirius\Admin\renders\table\ThinkerColumns;
use Yirius\Admin\renders\ThinkerTable;
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
     * @var string
     */
    protected $urlPath = '';

    /**
     * @var string 
     */
    protected $actionName = '';

    /**
     * 用户可执行规则
     * @var array
     */
    protected $authRules = [];

    protected function initialize()
    {
        $this->actionName = $this->request->action();
        if(!empty($this->actionName)){
            //拼装当前访问的url
            $this->urlPath = "/".$this->request->module()."/".$this->request->controller()."/".$this->actionName;
        } else {
            //如果不存在action，说明是通过router访问的
            $dispatch = $this->request->dispatch()->getDispatch();
            $this->actionName = $dispatch[count($dispatch)-1];
            //释放参数
            $dispatch = null;

            //说明是通过路由访问的，需要获取到路由规则
            $routeInfo = $this->request->routeInfo();
            //判断url是否有其他参数
            $this->urlPath = "/".$routeInfo['rule'];
            if(!empty($routeInfo['var'])) {
                //判断是否有自定义参数
                if(strpos($this->urlPath, "/<") >= 0){
                    foreach($routeInfo['var'] as $i => $item){
                        $this->urlPath = str_replace("<".$i.">", $item, $this->urlPath);
                    }
                }
            }
            //释放参数
            $routeInfo = null;
        }

        //记录，以便以后使用
        $_SERVER['__REQUESTURL'] = $this->urlPath;

        //先获取Auth
        $this->checkAuth();

        $this->_init();
    }

    protected function _init(){}

    /**
     * @title      getTokenInfo
     * @description 解析Token
     * @createtime 2020/5/27 8:59 下午
     * @author     yangyuance
     */
    protected function getTokenInfo() {

        $this->tokenInfo = ThinkerAdmin::jwt()->getTokenInfo();

        //判断单点登录
        if(ThinkerAdmin::properties()->getJwt("singleLogin")) {
            $recordIp = ThinkerAdmin::Cache()
                ->getAuthCache("loginip", $this->tokenInfo, "");

            if(empty($recordIp)){
                ThinkerAdmin::Cache()
                    ->setAuthCache("loginip", $this->tokenInfo, $this->request->ip());
            }else{
                if($this->request->ip() != $recordIp){
                    ThinkerAdmin::response()
                        ->msg("该账号存在他人登录，您已被强制下线")
                        ->fail();
                }
            }
        }

        //设置缓存判断当前的token状态, 缓存设置的过期时间
        ThinkerAdmin::Cache()->setTokenCache(
            "jwt",
            $this->tokenInfo,
            time(),
            ThinkerAdmin::properties()->getJwt("expiredOperateTime")
        );

        ThinkerAdmin::cache()->setTokenOperateTime($this->tokenInfo);
    }

    /**
     * @title      getAuthRules
     * @description
     * @createtime 2020/5/27 9:54 下午
     * @param array $type
     * @author     yangyuance
     */
    protected function getAuthRules(array $type = [1,2]) {
        if(!empty($this->tokenInfo)) {
            $this->authRules = ThinkerAdmin::cache()->getAuthCache(
                "shirourlrules_" . join("_", $type), $this->tokenInfo, null
            );

            if(empty($this->authRules)) {
                $this->authRules = [];

                foreach((new AdminRulesModel())->findUserRules($this->tokenInfo, $type) as $i => $authRule) {
                    if(!empty($authRule['url'])) {
                        $this->authRules[] = strtolower($authRule['url']);
                    }
                }

                ThinkerAdmin::cache()->setAuthCache(
                    "shirourlrules_" . join("_", $type), $this->tokenInfo, $this->authRules
                );
            }
        }
    }

    /**
     * @title      checkAuth
     * @description 检验规则
     * @createtime 2020/5/27 9:31 下午
     * @author     yangyuance
     */
    public function checkAuth() {
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

        if (isset($only) && !in_array($this->actionName, $only)) {
            //只验证这些
            $this->verifyUrlAuth();
        } elseif (isset($except) && in_array($this->actionName, $except)) {
            //在数组内的不验证，直接过
        } else {
            if($this->tokenAuth === false){
                //除非强制设置为false，否则都验证
            }else{
                //其他情况都需要验证
                $this->verifyUrlAuth();
            }
        }
    }

    /**
     * @title      verifyUrlAuth
     * @description
     * @createtime 2020/5/27 9:55 下午
     * @author     yangyuance
     */
    public function verifyUrlAuth() {
        //找到Token信息
        $this->getTokenInfo();

        //找到规则信息
        $this->getAuthRules();

        if(!empty($this->authRules)) {
            $isPass = false;$urlArr = [];
            foreach (explode("/", strtolower($this->urlPath)) as $i => $v) {
                $urlArr[] = $v;
                if(count($urlArr) > 2) {
                    if(in_array(strtolower(join("/", $urlArr)), $this->authRules)) {
                        $isPass = true;
                        break;
                    }
                }
            }
            if(!$isPass) {
                ThinkerAdmin::response()
                    ->msg("Auth信息失败: 您无法访问当前界面")
                    ->fail();
            }
        } else {
            ThinkerAdmin::response()
                ->msg("您无法在缺少鉴权的情况下访问")
                ->fail();
        }
    }

    /**
     * @title      getRulesMap
     * @description
     * @createtime 2020/5/27 10:08 下午
     * @return array|mixed
     * @author     yangyuance
     */
    protected function getRulesMap() {
        $rulesMap = ThinkerAdmin::cache()->getAuthCache(
            "rules_map", $this->tokenInfo, null
        );

        if(empty($rulesMap)) {
            $rulesMap = [];
            foreach((new AdminRulesModel())->findUserRules($this->tokenInfo) as $i => $rule) {
                if(!empty($rule['url'])) {
                    $rulesMap[strtolower($rule['url'])] = strtolower($rule['name']);
                }

                $rulesMap[strtolower($rule['name'])] = empty($rule['url']) ? "" : strtolower($rule['url']);
            }

            ThinkerAdmin::cache()->setAuthCache(
                "rules_map", $this->tokenInfo, $rulesMap
            );
        }

        return $rulesMap;
    }

    /**
     * @title      checkRuleName
     * @description
     * @createtime 2020/5/27 10:13 下午
     * @param array $ruleNames
     * @return array
     * @author     yangyuance
     */
    protected function checkRuleName(array $ruleNames) {
        $rulesMap = $this->getRulesMap();

        $booleans = [];

        if(isset($rulesMap[strtolower($this->urlPath)])) {
            $prefixName = $rulesMap[strtolower($this->urlPath)];

            foreach ($ruleNames as $name) {
                $booleans[] = isset($rulesMap[$prefixName.$name]);
            }
        }

        return $booleans;
    }

    /**
     * @title      renderTableRule
     * @description
     * @createtime 2020/5/27 10:01 下午
     * @param ThinkerTable $table
     * @param string       $title
     * @return ThinkerColumns
     * @author     yangyuance
     */
    protected function renderTableRule(ThinkerTable $table, $title = "操作")
    {
        //按钮是否存在
        $booleans = $this->checkRuleName([":add", ":del", ":edit"]);

        if(!empty($booleans)) {
            //存在其中一个
            if(!empty($booleans[0]) || !empty($booleans[1]) || !empty($booleans[2])) {
                $columns = $table->columns("op", $title);

                //判断后方按钮
                if(!empty($booleans[1]) || !empty($booleans[2])){
                    $columns->edit()->delete()->setWidth(145);
                    $table->colsEvent()->edit()->delete();
                } else if(!empty($booleans[2])){
                    $columns->edit()->setWidth(81);
                    $table->colsEvent()->edit();
                } else if(!empty($booleans[1])){
                    $columns->delete()->setWidth(81);
                    $table->colsEvent()->delete();
                }


                //判断上方按钮
                if(!empty($booleans[0]) || !empty($booleans[1])){
                    $table->toolbar()->add()->delete()->event()->add()->delete();
                } else if(!empty($booleans[0])){
                    $table->toolbar()->add()->event()->add();
                } else if(!empty($booleans[1])){
                    $table->toolbar()->delete()->event()->delete();
                }

                return $columns;
            }
        }

        return null;
    }

    /**
     * @title      verifyPassword
     * @description
     * @createtime 2020/5/27 10:17 下午
     * @param $password
     * @author     yangyuance
     */
    protected function verifyPassword($password) {
        if(empty($password) || !ThinkerAdmin::jwt()->verifyPassword($password)) {
            ThinkerAdmin::response()->msg(lang("incorrect username or password"))->fail();
        }
    }
}