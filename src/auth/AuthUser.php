<?php


namespace Yirius\Admin\auth;


use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use Yirius\Admin\ThinkerAdmin;

class AuthUser
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
        //菜单组
        'auth_menu' => 'teadmin_menu',
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
     * @title      setConfig
     * @description 设置参数
     * @createtime 2019/11/12 10:20 下午
     * @param array $config
     * @return $this
     * @author     yangyuance
     */
    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @title      getUser
     * @description 获取用户信息
     * @createtime 2019/11/12 10:13 下午
     * @param $value
     * @param $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author     yangyuance
     */
    public function getUser($value, $field)
    {
        return db($this->config['auth_user'][$this->config['access_type']])
            ->cache(
                'thinker_admin_authuser_' . $this->config['access_type'] . '_' . $value . "_" . $field,
                0,
                'thinker_admin_auth'
            )
            ->where($field, '=', $value)
            ->find();
    }

    /**
     * @title       getAuthGroups
     * @description 获取到用户组
     * @createtime  2019/11/12 10:03 下午
     * @param $userid
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author      yangyuance
     */
    public function getGroups($userid)
    {
        //查询这个用户id对应的用户组和权限
        return db($this->config['auth_group_access'])
            ->alias('a')
            ->field('a.uid,a.group_id,b.title,b.rules')
            ->join($this->config['auth_group'] . " b", "a.group_id=b.id", 'LEFT')
            ->where("a.uid", intval($userid))
            ->where("b.status", 1)
            ->where("a.type", $this->config['access_type'])
            ->cache(
                'thinker_admin_authgroup_' . $this->config['access_type'] . "_" . $userid,
                0,
                'thinker_admin_auth'
            )
            ->select();
    }

    /**
     * @title       _rulesSelect
     * @description 用户规则查询的底层逻辑
     * @createtime  2019/11/12 10:35 下午
     * @param     $userid
     * @param int $type
     * @return array|mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author      yangyuance
     */
    protected function _rulesSelect($userid, $type = 1)
    {
        //如果已经存在了
        if ($authList = cache("thinker_authlist_".$this->config['access_type']."_".$userid."_".$type)) {
            return $authList;
        }

        //首先找到用户组
        $userGroups = $this->getGroups($userid);

        //保存用户所属用户组设置的所有权限规则id
        $ruleids = [];
        foreach ($userGroups as $userGroup) {
            $ruleids = array_merge($ruleids, explode(',', trim($userGroup['rules'], ',')));
        }
        //去重，一个用户可能有多个权限组
        $ruleids = array_unique($ruleids);

        if (!is_array($ruleids) || empty($ruleids)) {
            return [];
        }

        //查询到所有规则可用的
        $rules = db($this->config['auth_rule'])
            ->field('id,pid,name,title,url,icon,condition')
            ->cache(
                "thinker_admin_authrulesall_" . $this->config['access_type'] . "_" . $userid . "_" . $type,
                0,
                'thinker_admin_auth'
            )
            ->where('id', 'in', $ruleids)
            ->where('status', '=', 1)
            ->where('type', '=', $type)
            ->order("list_order", "desc")
            ->select();

        //保存规则
        $authList = [];
        try {
            //获取用户信息,一维数组, 指定用户表内的
            $user = $this->getUser($userid, "id");

            foreach ($rules as $rule) {
                if (!empty($rule['condition'])) {
                    //根据condition进行验证
                    $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                    $condition = null;
                    @(eval('$condition=(' . $command . ');'));
                    if ($condition) {
                        $authList[] = [
                            'id'    => $rule['id'],
                            'name'  => strtolower($rule['name']),
                            'title' => $rule['title'],
                            'pid'   => $rule['pid'],
                            'url'   => $rule['url'],
                            'icon'  => $rule['icon']
                        ];
                    }
                } else {
                    //只要存在就记录
                    $authList[] = [
                        'id'    => $rule['id'],
                        'name'  => strtolower($rule['name']),
                        'title' => $rule['title'],
                        'pid'   => $rule['pid'],
                        'url'   => $rule['url'],
                        'icon'  => $rule['icon']
                    ];
                }
            }

            cache(
                "thinker_authlist_".$this->config['access_type']."_".$userid."_".$type,
                $authList,
                0,
                'thinker_admin_auth'
            );

            return $authList;
        } catch (\Exception $e) {
            trace($this->config['access_type']."_".$userid."_".$type."查询规则失败", "error");
            return [];
        }
    }

    /**
     * @title       getRules
     * @description 获取到指定用户可以使用的规则
     * @createtime  2019/11/12 10:15 下午
     * @param     $userid
     * @param int $type
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author      yangyuance
     */
    public function getRules($userid, $type = 1)
    {
        if($authRules = cache("thinker_authrules_".$this->config['access_type']."_".$userid."_".$type))
        {
            return $authRules;
        }

        //找到底层的数据
        $authRules = $this->_rulesSelect($userid, $type);
        //记录可以使用的规则
        $useRules = [];
        for($i = 0; $i < count($authRules); $i++){
            $useRules[] = $authRules[$i]['name'];
        }

        cache(
            "thinker_authrules_".$this->config['access_type']."_".$userid."_".$type,
            $useRules,
            0,
            'thinker_admin_auth'
        );

        return $useRules;
    }

    /**
     * @title      getMenus
     * @description
     * @createtime 2019/11/12 10:43 下午
     * @param $userid
     * @return array|mixed
     * @author     yangyuance
     */
    public function getMenus($userid)
    {
        if($authMenus = cache("thinker_authmenus_".$this->config['access_type']."_".$userid))
        {
            return $authMenus;
        }

        //找到底层的数据
        $useMenus = ThinkerAdmin::Tree()->setConfig([
            'sublist' => "childs"
        ])->setItemEach(function($value){
            return [
                'title' => $value['title'],
                'icon'  => $value['icon'],
                'href'  => $value['url']
            ];
        })->tree($this->_rulesSelect($userid, 1));

        cache(
            "thinker_authmenus_".$this->config['access_type']."_".$userid,
            $useMenus,
            0,
            'thinker_admin_auth'
        );

        return $useMenus;
    }
}