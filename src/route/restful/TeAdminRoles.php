<?php


namespace Yirius\Admin\route\restful;


use think\db\Query;
use Yirius\Admin\extend\ThinkerRestful;

class TeAdminRoles extends ThinkerRestful
{
    protected $_UseTable = \Yirius\Admin\route\model\TeAdminRoles::class;

    protected $_Where = [
        "status",
        "title" => ['title', 'like', '%_var%']
    ];

    protected $_Validate = [[
        'title' => "require",
        'rules'  => "require",
    ], [
        'title.require' => "角色名称必须填写",
        'rules.require' => "使用规则必须选择",
    ]];

    protected $_NotDelete = [1];
}