<?php


namespace Yirius\Admin\admin\restful;


use think\App;
use Yirius\Admin\admin\model\AdminLogsModel;
use Yirius\Admin\admin\model\AdminRulesModel;
use Yirius\Admin\extend\ThinkerRestful;
use Yirius\Admin\ThinkerAdmin;

class TeAdminRules extends ThinkerRestful
{
    protected $tokenAuth = false;

    public function __construct(App $app = null)
    {
        parent::__construct($app);

        $this->_UseTable = AdminRulesModel::class;

        $this->_Where = [
            "pid", "type",
            "url" => ['url', 'like', '%_var%']
        ];

        $this->_Validate = [[
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
    }

    /**
     * @title      quickAdd
     * @description
     * @createtime 2020/5/28 1:18 下午
     * @author     yangyuance
     */
    public function quickAdd() {
        $param = input('param.');
        $param = ThinkerAdmin::validate()->make($param, [
            'name' => "require",
            'title' => "require",
            'url' => "requireIf:type,1",
            'list_order' => "require"
        ], [
            'name.require' => "规则英文名称必须填写",
            'title.require' => "规则中文名称必须填写",
            'url.require' => "您选择了菜单界面，必须填写网址",
            'list_order.require' => "排序必须填写"
        ]);

        $entity = $this->fillEntity($param, false);
        $entity['status'] = 1;

        $useTable = $this->getUseTable();
        if($useTable->save($entity)) {
            $saveBatchs = [];$listOrder = $entity['list_order'];
            if(!empty($param['tableconf'])) {
                foreach($param['tableconf'] as $item) {
                    $title = "新增";
                    switch ($item) {
                        case "add":
                            $title = "新增";
                            break;
                        case "del":
                            $title = "删除";
                            break;
                        case "edit":
                            $title = "修改";
                            break;
                        default:
                            $title = $item;
                    }
                    $saveBatchs[] = [
                        'pid' => $useTable->id,
                        'name' => $entity['name'] . ":" . $item,
                        'title' => $entity['title'] . "-" . $title,
                        'status' => 1,
                        'type' => 3,
                        'list_order' => --$listOrder,
                        'url' => ""
                    ];
                }

                if(in_array('add', $param['tableconf']) || in_array('edit', $param['tableconf'])) {
                    if(!empty($param['opurl'])) {
                        $saveBatchs[] = [
                            'pid' => $useTable->id,
                            'name' => $entity['name'] . ":editpage",
                            'title' => $entity['title'] . "-新增/修改界面",
                            'status' => 1,
                            'type' => 2,
                            'list_order' => --$listOrder,
                            'url' => $param['url'] . "Edit"
                        ];
                    }
                }
            }

            if(!empty($param['restfulurl'])) {
                $saveBatchs[] = [
                    'pid' => $useTable->id,
                    'name' => $entity['name'] . ":restful",
                    'title' => $entity['title'] . "-RestfulApi",
                    'status' => 1,
                    'type' => 2,
                    'list_order' => --$listOrder,
                    'url' => $param['restfulurl']
                ];
            }

            if(!empty($saveBatchs)) {
                if($this->getUseTable()->saveAll($saveBatchs)) {
                    ThinkerAdmin::response()->msg("生成规则成功")->success();
                }

                ThinkerAdmin::response()->msg("生成规则失败, 已生成顶级规则")->fail();
            }

            ThinkerAdmin::response()->msg("生成规则成功")->success();
        } else {
            ThinkerAdmin::response()->msg("生成规则失败")->fail();
        }
    }

    protected function _afterSave(array $entity, $isUpdate = [])
    {
        (new AdminLogsModel())->addLog($this->tokenInfo, "保存规则-" . $entity['id'], false);
    }

    protected function _afterUpdate(array $entity)
    {
        (new AdminLogsModel())->addLog($this->tokenInfo, "修改规则-" . $entity['id'], false);
    }

    protected function _afterDelete(array $deledArr)
    {
        (new AdminLogsModel())->addLog($this->tokenInfo,
            "删除规则-" . json_encode($deledArr), false
        );
    }
}