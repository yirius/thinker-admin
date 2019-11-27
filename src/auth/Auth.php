<?php


namespace Yirius\Admin\auth;


use Yirius\Admin\ThinkerAdmin;

class Auth
{
    /**
     * @var array
     */
    protected $config = [
        //用户组数据表名, 比如id=1,access_type=0的用户，可能由多个用户组
        'auth_group' => 'teadmin_group',
        //用户-用户组关系表
        'auth_group_access' => 'teadmin_group_access',
        //权限规则表
        'auth_rule' => 'teadmin_rules',
        //登录使用的用户表组
        'auth_user' => [
            'teadmin_member'
        ],
        //当前验证使用的用户类型
        'access_type' => 0,
        //登录验证的字段
        'login_field' => [
            "username|phone"
        ]
    ];

    /**
     * @var AuthUser|null
     */
    protected $authUser = null;

    /**
     * Auth constructor.
     * @param array|null $config
     */
    public function __construct(array $config = null)
    {
        if (is_null($config)) {
            $config = config("thinkeradmin.auth");
        }

        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }

        //声明AuthUser
        $this->authUser = (new AuthUser())->setConfig($this->config);
    }

    /**
     * @title      getConfig
     * @description
     * @createtime 2019/11/27 8:46 下午
     * @param null $name
     * @return array
     * @author     yangyuance
     */
    public function getConfig($name = null)
    {
        if(is_null($name)){
            return $this->config;
        }else{
            return isset($this->config[$name]) ? $this->config[$name] : $this->config;
        }
    }

    /**
     * @title      setAccessType
     * @description
     * @createtime 2019/11/12 9:59 下午
     * @param $access_type
     * @return $this
     * @author     yangyuance
     */
    public function setAccessType($access_type)
    {
        $this->config['access_type'] = intval($access_type);
        if (isset($this->config['auth_user'][$this->config['access_type']])) {
            $this->authUser->setConfig($this->config);
        } else {
            ThinkerAdmin::Send()->json([], 0, "access_type [index] not exist in auth_user");
        }
        return $this;
    }

    /**
     * @title      getAccessType
     * @description
     * @createtime 2019/11/12 10:01 下午
     * @return int
     * @author     yangyuance
     */
    public function getAccessType()
    {
        return $this->config['access_type'];
    }

    /**
     * @title      getUser
     * @description
     * @createtime 2019/11/12 11:10 下午
     * @param $value
     * @param $field
     * @return array|null
     * @author     yangyuance
     */
    public function getUser($value, $field = null)
    {
        try{
            if(is_null($field)){
                $field = $this->config['login_field'][$this->config['access_type']];
            }
            return $this->authUser->getUser($value, $field);
        }catch (\Exception $exception){
            return null;
        }
    }

    /**
     * @title      getGroups
     * @description
     * @createtime 2019/11/12 11:10 下午
     * @param $userid
     * @return array
     * @author     yangyuance
     */
    public function getGroups($userid)
    {
        try{
            return $this->authUser->getGroups($userid);
        }catch (\Exception $exception){
            return [];
        }
    }

    /**
     * @title      getRules
     * @description
     * @createtime 2019/11/12 11:11 下午
     * @param     $userid
     * @param int $type
     * @return array
     * @author     yangyuance
     */
    public function getRules($userid, $type = 1)
    {
        try{
            return $this->authUser->getRules($userid, $type);
        }catch (\Exception $exception){
            return [];
        }
    }

    /**
     * @title      getMenus
     * @description
     * @createtime 2019/11/12 11:12 下午
     * @param $userid
     * @return array|mixed
     * @author     yangyuance
     */
    public function getMenus($userid)
    {
        try{
            return $this->authUser->getMenus($userid);
        }catch (\Exception $exception){
            return [];
        }
    }

    /**
     * @title      checkRule
     * @description
     * @createtime 2019/11/13 12:53 下午
     * @param        $rules
     * @param        $userid
     * @param int    $type
     * @param string $relation
     * @return bool
     * @author     yangyuance
     */
    public function checkRule($rules, $userid, $type = 1, $relation = 'or')
    {
        $useRules = $this->getRules($userid, $type);

        if (is_string($rules)) {
            $rules = explode(',', strtolower($rules));
        }

        $canUserRules = [];
        foreach ($useRules as $useRule) {
            if (in_array($useRule, $rules)) {
                $canUserRules[] = $useRule;
            }
        }

        if ('or' == $relation && !empty($canUserRules)) {
            return true;
        }

        $diff = array_diff($rules, $canUserRules);
        if ('and' == $relation && empty($diff)) {
            return true;
        }

        return false;
    }

    /**
     * @title      checkUrl
     * @description
     * @createtime 2019/11/15 7:21 下午
     * @param     $url
     * @param     $userid
     * @param int|array $type
     * @return bool
     * @author     yangyuance
     */
    public function checkUrl($url, $userid, $type = 1)
    {
        try{
            $useRules = $this->authUser->_rulesSelect($userid, $type);
        }catch (\Exception $exception){
            $useRules = [];
        }

        //找到那些路径是可用的
        $canUseUrl = [];
        foreach($useRules as $useRule){
            if(!empty($useRule['url'])){
                $canUseUrl[] = strtolower($useRule['url']);
            }
        }

        //判断当前Url是否可用
        if(in_array(strtolower($url), $canUseUrl)){
            return true;
        }else{
            return false;
        }
    }
}