<?php
/**
 * 惯例配置
 */
return [
    //是否开启多页版本
    'isIframe' => false,

    //Auth相关配置
    'auth' => [
        'auth_group' => 'ices_admin_group', // 用户组数据表名
        'auth_group_access' => 'ices_admin_group_access', // 用户-用户组关系表
        'auth_rule' => 'ices_admin_rule', // 权限规则表
        'auth_menu' => 'ices_admin_menu',//菜单表
        'auth_user' => [
            'ices_admin_member'
        ],
        'access_type' => 0,
        'login_field' => "username|phone"
    ],

    //Rule相关配置
    'rule' => [
        'type' => [
            ['text' => "其他规则", 'value' => 3]
        ]
    ],

    //json web token相关设置
    'jwt' => [
        'type' => "HS256",
        'key' => "",
        'rsakey' => [
            'privatekey' => '',
            'publickey' => '',
        ],
        'notbefore' => 0,
        'expire' => 43200//0标识不过期
    ],

    //form相关
    'form' => [
        'extends' => []
    ]
];