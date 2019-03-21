<?php
/**
 * 惯例配置
 */
return [
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
        'login_field' => "username|phone",
        'login_verfiy_func' => null,
        'login_update_func' => null
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
    ],

    //Upload配置
    'upload' => [
        'images' => [
            'water' => false,
            'validate' => [
                'size' => 1024 * 1024 * 5,
                'ext' => "jpg,png,gif,jpeg,do,bmp"
            ]
        ],
        'files' => [
            'size' => 1024 * 1024 * 5,
            'ext' => "png,jpg,jpeg,gif,bmp,flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogg,ogv,mov,wmv,mp4,webm,mp3,wav,mid,rar,zip,tar,gz,7z,bz2,cab,iso,doc,docx,xls,xlsx,ppt,pptx,pdf,txt,md,xml"
        ]
    ],

    //Widgets插件管理
    'widgets' => [
        'breadcrumb' => \Yirius\Admin\widgets\Breadcrumb::class,
        'card' => \Yirius\Admin\widgets\Card::class
    ]
];