<?php

// +----------------------------------------------------------------------
// | ThinkerAdmin设置
// +----------------------------------------------------------------------
return [
    //log记录的一些参数
    'log' => [
        //是否记录每一次http请求
        'http' => true,
        //不记录堆栈的执行
        'nostack' => ['info'],
        //不记录http请求的前缀
        'nohttp' => [
            //获取菜单不记录
            '/thinkeradmin/admin/menus',
            //展示页不记录
            '/thinkeradmin/show/index',
            //系统管理访问不记录
            '/thinkeradmin/system/rules',
            '/thinkeradmin/system/roles',
            '/thinkeradmin/system/users',
            '/restful/thinkeradmin/teadminroles',
            '/restful/thinkeradmin/teadminusers',
            '/restful/thinkeradmin/teadminrules',
            //监控的所有
            '/thinkeradmin/logs/system',
            '/thinkeradmin/logs/login',
            '/thinkeradmin/logs/http',
            '/thinkeradmin/logs/log',
            '/restful/thinkeradmin/teadminlogs'
        ]
    ],
    //验证Auth权限使用的参数
    'auth' => [
        //是否单点登录
        'singleLogin' => true,
        //token的名称
        'token_name' => "Access-Token",
        //验证码是否开启
        'vercode' => true,
        //登录错误次数，0为不限制
        'login_error_count' => 0,
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
        ],
        //登录时候的自定义验证方式
        'login_verfiy_func' => null
    ],
    //Json Web Token参数
    'jwt' => [
        //是否使用rsa加密
        'isRsa' => false,
        //秘钥
        'key' => "131313131231312131",
        //使用的rsakey
        'rsakey' => [
            'privatekey' => '',
            'publickey' => '',
        ],
        'notbefore' => 0,
        'expire' => 86400,//0标识不过期
        //过期jwt返回重新登录状态
        'expired_code' => 1001,
        //设置多长时间内没有auth验证操作就不在续期token
        'expired_operate_time' => 7200,
        //如果当前设定时间内存在验证Auth的操作，就返回重新设置token的状态
        'reexpired_code' => 1002
    ],
    //Menu的过滤函数，args: $menus, $this->tokenInfo, $this->auth
    'menu_fliter' => null
];