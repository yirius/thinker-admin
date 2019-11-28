<?php


namespace Yirius\Admin\route\restful;


use think\db\Query;
use think\Model;
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

    protected function _afterSave($isUpdate, array $saveData, Model $model)
    {
        thinker_log($this->tokenInfo, $isUpdate ? "编辑角色信息" : "新增角色信息");
    }

    protected function _afterUpdate($id, $field, array $saveData, Model $model)
    {
        thinker_log($this->tokenInfo, "编辑字段:".$field);
    }

    protected function _afterDelete(array $errorIds)
    {
        thinker_log($this->tokenInfo, "删除角色信息");
    }
}