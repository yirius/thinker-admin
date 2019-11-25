<?php


namespace Yirius\Admin\route\restful;


use think\db\Query;
use Yirius\Admin\extend\ThinkerRestful;

class TeAdminRules extends ThinkerRestful
{
    protected $_UseTable = \Yirius\Admin\route\model\TeAdminRules::class;

    protected $_Where = [
        "pid", "type",
        "url" => ['url', 'like', '%_var%']
    ];

    protected $_Validate = [[
        'name'  => "require",
        'title' => "require",
        'type'  => "require",
        'url'   => "requireIf:type,1",
        'icon'  => "requireIf:type,1",
        'list_order' => "require",
    ], [
        'name.require'  => "规则英文必须填写",
        'title.require' => "规则名称必须填写",
        'type.require'  => "规则类型必须选择",
        'url.require'   => "您选择了菜单栏目，必须填写对应网址",
        'icon.require'  => "您选择了菜单类目，必须填写对应图标",
        'list_order.require' => "规则排序必须填写",
    ]];

    protected $_NotDelete = [];
}