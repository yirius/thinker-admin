<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/20
 * Time: 下午11:59
 */

namespace Yirius\Admin\auth;


use Yirius\Admin\Admin;

class Auth
{
    /**
     * @var array
     */
    protected $config = [
        'auth_group' => 'ices_admin_group', // 用户组数据表名
        'auth_group_access' => 'ices_admin_group_access', // 用户-用户组关系表
        'auth_rule' => 'ices_admin_rule', // 权限规则表
        'auth_menu' => 'ices_admin_menu',
        'auth_user' => [
            'ices_admin_member'
        ],
        'access_type' => 0,
        'login_field' => "username|phone"
    ];

    /**
     * @var int
     */
    protected $accessType = 0;

    /**
     * @var string
     */
    protected $authUserTable;

    /**
     * Auth constructor.
     * @param array|null $config
     * @throws \Exception
     */
    public function __construct(array $config = null)
    {
        if (is_null($config)) {
            $config = config("thinkeradmin.auth");
        }

        if (is_array($config)) {
            $this->setConfig($config);
        }
    }

    /**
     * @title setConfig
     * @description
     * @createtime 2019/2/21 上午12:26
     * @param array $config
     * @return $this
     * @throws \Exception
     */
    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);

        //auto set access_type
        $this->setAccessType($this->config['access_type']);

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
     * @title setAccessType
     * @description
     * @createtime 2019/2/21 上午12:26
     * @param $access_type
     * @return $this
     * @throws \Exception
     */
    public function setAccessType($access_type)
    {
        $this->accessType = $access_type;

        if (!empty($this->config['auth_user'][$this->accessType])) {
            $this->authUserTable = $this->config['auth_user'][$this->accessType];
        } else {
            throw new \Exception("access_type [index] not exist in auth_user");
        }

        return $this;
    }

    /**
     * @title check
     * @description
     * @createtime 2019/2/26 下午2:45
     * @param string $rules 判断规则,可以使字符串可以是数组,字符串用【,】分割也可以,判断的标准 true '' ''
     * @param int $userid
     * @param int $type
     * @param string $relation 关联规则的模式,是或还是与 or or|and
     * @return bool
     */
    public function check($rules, $userid, $type = 1, $relation = 'or')
    {
        $authRules = $this->getAuthRules($userid, $type);

        if (is_string($rules)) {
            $rules = explode(',', strtolower($rules));
        }

        $canUserRules = [];
        foreach ($authRules as $authRule) {
            if (in_array($authRule, $rules)) {
                $canUserRules[] = $authRule;
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
     * @title getAuthRules
     * @description 获取到所有该用户可用的规则， 在指定类型下
     * @createtime 2019/2/21 上午1:25
     * @param $userid
     * @param int $type
     * @return array|mixed
     */
    public function getAuthRules($userid, $type = 1)
    {
        if ($ruleids = cache("thinker_admin_authrules_" . $this->accessType . "_" . $userid . "_" . $type)) {
            return $ruleids;
        } else {

            $authGroups = $this->getAuthGroups($userid);

            $ruleids = []; //保存用户所属用户组设置的所有权限规则id
            foreach ($authGroups as $g) {
                $ruleids = array_merge($ruleids, explode(',', trim($g['rules'], ',')));
            }

            $ruleids = array_unique($ruleids);

            //if rule is empty, then return
            if (!is_array($ruleids) || empty($ruleids)) {
                return [];
            }

            //查询到所有规则可用的
            $rules = db($this->config['auth_rule'])
                ->field('condition,name')
                ->cache(
                    "thinker_admin_authrulesall_" . $this->accessType . "_" . $userid . "_" . $type,
                    null,
                    'thinker_admin_auth'
                )
                ->where('id', 'in', $ruleids)
                ->where('status', '=', 1)
                ->where('type', '=', $type)
                ->select();

            $authList = []; //循环规则，判断结果。
            $user = $this->getUserinfo($userid, "id"); //获取用户信息,一维数组, 指定用户表内的
            foreach ($rules as $rule) {
                if (!empty($rule['condition'])) {
                    //根据condition进行验证
                    $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                    //dump($command); //debug
                    $condition = null;
                    @(eval('$condition=(' . $command . ');'));
                    if ($condition) {
                        $authList[] = strtolower($rule['name']);
                    }
                } else {
                    //只要存在就记录
                    $authList[] = strtolower($rule['name']);
                }
            }

            $authList = array_unique($authList);

            cache(
                "thinker_admin_authrules_" . $this->accessType . "_" . $userid . "_" . $type,
                $authList,
                null,
                'thinker_admin_auth'
            );

            return $authList;
        }
    }

    /**
     * @title getAuthGroups
     * @description user $userid to find user groups rule
     * @createtime 2019/2/21 上午1:08
     * @param $userid
     * @return mixed
     */
    public function getAuthGroups($userid)
    {
        // 执行查询
        $authGroups = db($this->config['auth_group_access'])
            ->alias('a')
            ->field('uid,group_id,title,rules')
            ->join($this->config['auth_group'] . " b", "a.group_id=b.id", 'LEFT')
            ->where("a.uid", $userid)
            ->where("b.status", 1)
            ->where("a.type", $this->accessType)
            ->cache(
                'thinker_admin_authgroup_' . $this->accessType . "_" . $userid,
                0,
                'thinker_admin_auth'
            )
            ->select();

        return $authGroups;
    }

    /**
     * @title getAuthMenu
     * @description
     * @createtime 2019/2/26 下午2:36
     * @param $userid
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAuthMenu($userid)
    {
        if($result = cache("thinker_admin_authmenu_" . $this->accessType . "_" . $userid))
        {
            return $result;
        }

        $authRules = $this->getAuthRules($userid);

        $menus = db($this->config['auth_menu'])
            ->order('sort', 'desc')
            ->select();

        $menuList = [];
        foreach ($menus as $key => $value) {
            $jump = strtolower($value['jump']);
            if (in_array($jump, $authRules)) {
                $menuList[] = $value;
            }
        }

        $result = Admin::tools()->tree($menuList, function($data){
            return [
                'id' => $data['id'],
                'name' => $data['name'],
                'title' => $data['title'],
                'jump' => $data['jump'],
                'icon' => $data['icon']
            ];
        });

        cache(
            "thinker_admin_authmenu_" . $this->accessType . "_" . $userid,
            $result,
            null,
            "thinker_admin_auth"
        );

        return $result;
    }

    /**
     * @title getUserinfo
     * @description get userinfo
     * @createtime 2019/2/21 上午1:29
     * @param $value
     * @param null $field
     * @return mixed
     */
    public function getUserinfo($value, $field = null)
    {
        //judge login field
        if (is_null($field)) {
            $field = $this->config['login_field'];
        }

        return db($this->authUserTable)
            ->cache(
                'thinker_admin_authuser_' . $this->accessType . '_' . $value . "_" . $field,
                0,
                'thinker_admin_auth'
            )
            ->where($field, '=', $value)
            ->find();
    }
}