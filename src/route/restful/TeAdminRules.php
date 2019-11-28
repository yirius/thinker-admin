<?php


namespace Yirius\Admin\route\restful;


use think\Db;
use think\db\Query;
use think\Model;
use Yirius\Admin\extend\ThinkerRestful;
use Yirius\Admin\services\MigrateRule;
use Yirius\Admin\ThinkerAdmin;

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
//        'url'   => "requireIf:type,1",
//        'icon'  => "requireIf:type,1",
        'list_order' => "require",
    ], [
        'name.require'  => "规则英文必须填写",
        'title.require' => "规则名称必须填写",
        'type.require'  => "规则类型必须选择",
//        'url.require'   => "您选择了菜单栏目，必须填写对应网址",
//        'requireIf.require'  => "您选择了菜单类目，必须填写对应图标",
        'list_order.require' => "规则排序必须填写",
    ]];

    protected function _beforeSave(array $params, $updateWhere)
    {
        if(input('param.__type') == "quickadd"){

            $params = ThinkerAdmin::Validate()->make($params, [
                'url' => "require",
            ], [
                'url.require' => "网址必须填写",
            ]);

            $tableConfig = input('param.tableconf', []);

            $lastId = (new \Yirius\Admin\route\model\TeAdminRules())->order("id", "desc")->find();
            $result = (new MigrateRule())
                ->setId($lastId->id + 1)
                ->setName($params['name'])
                ->setPid($params['pid'])
                ->setStatus(input('status', 1))
                ->setTitle($params['title'])
                ->setType($params['type'])
                ->setUrl($params['url'])
                ->setListOrder($params['list_order'])
                ->setRestful(input('param.restfulurl', ''))
                ->setTableConf($tableConfig)
                ->setOperatePage(input('param.opurl', ''))
                ->getResult();

            Db::name(config('thinkeradmin.auth.auth_rule'))->insertAll($result);

            thinker_log($this->tokenInfo, "快捷新增规则信息");

            ThinkerAdmin::Send()->json();
        }else{
            return $params;
        }
    }

    protected function _afterSave($isUpdate, array $saveData, Model $model)
    {
        thinker_log($this->tokenInfo, $isUpdate ? "编辑规则信息" : "新增规则信息");
    }

    protected function _afterUpdate($id, $field, array $saveData, Model $model)
    {
        thinker_log($this->tokenInfo, "编辑字段:".$field);
    }

    protected $_NotDelete = [];
}