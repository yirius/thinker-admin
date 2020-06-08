<?php


namespace Yirius\Admin\admin\controller;


use Yirius\Admin\admin\model\AdminGroupAccessModel;
use Yirius\Admin\admin\model\AdminGroupModel;
use Yirius\Admin\admin\model\AdminMemberModel;
use Yirius\Admin\admin\model\AdminRulesModel;
use Yirius\Admin\config\ConsConfig;
use Yirius\Admin\extend\ThinkerController;
use Yirius\Admin\renders\form\assemblys\Button;
use Yirius\Admin\renders\form\assemblys\TreePlus;
use Yirius\Admin\renders\form\ThinkerInline;
use Yirius\Admin\renders\page\ThinkerCard;
use Yirius\Admin\renders\ThinkerForm;
use Yirius\Admin\renders\ThinkerPage;
use Yirius\Admin\renders\ThinkerTable;
use Yirius\Admin\templates\TemplateList;
use Yirius\Admin\ThinkerAdmin;

class System extends ThinkerController
{
    /**
     * @title       getRuleTree
     * @description 获取到规则树列表
     * @return      {@link ThinkerCard}
     **@author YangYuanCe
     */
    private function getRuleTree()
    {
        $adminRulesList = (new AdminRulesModel())
            ->order("pid", "asc")
            ->order("list_order", "desc")
            ->select()
            ->toArray();

        $treeValues = ThinkerAdmin::tree()->setItemEach(function ($value) {
            return [
                'text' => $value['title'],
                'value' => $value['id'],
                'id' => $value['id'],
                'pid' => $value['pid']
            ];
        })->tree($adminRulesList);

        if (count($treeValues) > 0) {
            $treeValues[0]['spread'] = true;
            if (!empty($treeValues[0]['childs'])) {
                $treeValues[0]['childs'][0]['spread'] = true;
            }
        }

        ThinkerAdmin::script(TemplateList::admin()->RuleButtonJs()->templates([

        ])->render());

        return (new ThinkerCard(function (ThinkerCard $thinkerCard) use ($treeValues) {
            $thinkerCard->addCardHeaderClass("thinker-pad10");
            $thinkerCard->addHeaderLayout(
                (new Button("", "快捷添加界面逻辑"))->sm()->setId("popup_thinker_rule")
            );
            $thinkerCard->addBodyLayout(
                (new TreePlus("tree", ""))->setEdit(["add", "update", "del"])
                    ->setBeforeOperateEvent(TemplateList::admin()->RuleTreeJs()->templates([
                        "tree_rules", "/restful/thinkeradmin/TeAdminRules", '{id: 0, text: obj.data.text, pid: obj.data.pid, name: "", title: "", status: 1, type: 1, url: "", icon: "", listOrder: 1000}'
                    ])->render())->setData($treeValues)
            );
        }));
    }

    /**
     * @title       getRuleForm
     * @description 获取到规则提交FORM
     * @return      {@link ThinkerForm}
     **@author YangYuanCe
     */
    private function getRuleForm()
    {
        return ThinkerAdmin::form(function ($thinkerForm) {
            $thinkerForm->setTrimId("tree_rules");

            $thinkerForm->hidden("id", "")->setValue(0);

            $thinkerForm->text("pid", "上级编号");

            $thinkerForm->text("name", "规则英文");

            $thinkerForm->text("title", "规则名称");

            $thinkerForm->switchs("status", "规则状态");

            $thinkerForm->select("type", "规则类型")->options([
                ['text' => "菜单栏目", 'value' => 1],
                ['text' => "非菜单界面", 'value' => 2],
                ['text' => "界面权限", 'value' => 3],
            ]);

            $thinkerForm->text("url", "对应网址");

            $thinkerForm->iconpicker("icon", "对应图标");

            $thinkerForm->text("list_order", "规则排序");

        })->submit("/restful/thinkeradmin/TeAdminRules{{parseInt(d.id)?'/'+d.id:''}}", 0, null,
            "function(obj, url){\n" .
            "   return {\n" .
            "       method: parseInt(obj.field.id) ? \"put\" : \"post\"\n" .
            "   };\n" .
            "}"
        );
    }

    /**
     * @title       rules
     * @description 规则下拉界面
     * @return      {@link String}
     **@author YangYuanCe
     */
    public function rules()
    {
        return (new ThinkerPage(function (ThinkerPage $thinkerPage) {
            $rows = $thinkerPage->rows(null)->space(10);

            $rows->cols(null)->setSm(7)
                ->addLayout($this->getRuleTree())->addAttr("style", "padding: 0 5px");

            $rows->cols(null)->setSm(5)
                ->addLayout($this->getRuleForm())->addClass("bg-white");
        }))->setTitle("规则管理")->render();
    }

    /**
     * @title      rulesEdit
     * @description
     * @createtime 2020/5/28 1:34 下午
     * @return string|void
     * @author     yangyuance
     */
    public function rulesEdit()
    {
        return ThinkerAdmin::form(function (ThinkerForm $thinkerForm) {

            $adminRules = (new AdminRulesModel())
                ->order("pid", "asc")
                ->order("list_order", "desc")
                ->where('type', '=', 1)
                ->field("id,title,name,pid")
                ->select()
                ->toArray();

            $ruleTrees = ThinkerAdmin::tree()->setItemEach(function ($value) {
                return [
                    'text' => $value['title'] . "->" . $value['name'],
                    'value' => $value['id'],
                    'id' => $value['id'],
                    'pid' => $value['pid']
                ];
            })->tree($adminRules);

            $options = [];
            foreach($ruleTrees as $i => $ruleTree) {
                unset($ruleTree['childs']);
                $options[] = $ruleTree;
                //如果还有下级
                if(!empty($ruleTrees[$i]["childs"])) {
                    $options1 = [];
                    foreach($ruleTrees[$i]["childs"] as $j => $child) {
                        if(!empty($child['childs'])) {
                            unset($child['childs']);
                            $options1[] = $child;
                        }
                        $options1[] = $ruleTrees[$i]["childs"][$j];
                    }
                    $ruleTrees[$i]["childs"] = $options1;
                }
                $options[] = $ruleTrees[$i];
            }

            $thinkerForm->selectplus("pid", "上级编号")->options(
                $options
            )->setClickClose(true)->radio()->search()->setTree("{show:true,showFolderIcon:true,showLine:true}");

            $thinkerForm->text("name", "规则英文");

            $thinkerForm->text("title", "规则名称");

            $thinkerForm->select("type", "规则类型")->options([
                ['text' => "菜单栏目", 'value' => 1],
                ['text' => "非菜单界面", 'value' => 2],
            ]);

            $thinkerForm->text("url", "对应网址");

            $thinkerForm->text("restfulurl", "对应restful网址");

            $thinkerForm->switchs("opurl", "对应操作编辑网址");

            $thinkerForm->checkbox("tableconf[]", "界面设置")->options([
                ['text' => "添加", 'value' => "add"],
                ['text' => "删除", 'value' => "del"],
                ['text' => "修改", 'value' => "edit"],
            ]);

            $thinkerForm->text("list_order", "规则排序");

            $id = $thinkerForm->getId();
            $thinkerForm->button("autofill", "自动填充")
                ->on("click", "arguments[0].preventDefault();" .
                    "var name = $('#{$id}_name').val();" .
                    "if(name != '') {" .
                    "name = name.split(':').join('/');" .
                    "$('#{$id}_url').val('/'+name);" .
                    "$('#{$id}_restfulurl').val('/restful/'+name)" .
                    "}")->sm()->addClass("bg-cyan")
                ->addAttr("style", "position: absolute;right: 20px;top: 67px;");

        })->submit("/restful/thinkeradmin/TeAdminRules/quickadd")->send();
    }


    /**
     * @title roles
     * @description 角色管理
     * @author YangYuanCe
     * @return {@link String}
     **/
    public function roles() {
        return ThinkerAdmin::table(function(ThinkerTable $thinkerTable) {

            $thinkerTable->restful("/restful/thinkeradmin/TeAdminRoles")
                    ->setOperateUrl("/thinkeradmin/system/rolesEdit");

            $thinkerTable->search(function($thinkerInline) {
                $thinkerInline->text("title", "角色名称");

                $thinkerInline->select("status", "角色状态")->setPlaceholder()->options([
                    ['text' => "可使用", 'value' => 1],
                    ['text' => "已禁止", 'value' => 0],
                ]);
            });

            $thinkerTable->checkbox();

            $thinkerTable->columns("id", "ID")->setSort(true)->setMinWidth(80);

            $thinkerTable->columns("title", "角色名称")->setMinWidth(120);

            $thinkerTable->columns("status", "角色状态")->switchs("status")->setMinWidth(150);

            $thinkerTable->columns("access_type", "用户类型")->setMinWidth(120);

            $thinkerTable->columns("rules", "规则数量")
                ->setTemplet("<div>{{d.rules.split(',').length}}种</div>")->setMinWidth(80);

            $this->renderTableRule($thinkerTable);
        })->send("角色管理");
    }

    /**
     * @title rolesEdit
     * @description 角色编辑
     * @author YangYuanCe
     * @param id
     * @return {@link String}
     **/
    public function rolesEdit($id = null) {
        return ThinkerAdmin::form(function(ThinkerForm $thinkerForm) use($id){

            if(!empty($id)){
                $thinkerForm->setUseValue(AdminGroupModel::get(intval($id))->toArray());
            }

            $thinkerForm->text("title", "角色名称");

            $thinkerForm->switchs("status", "状态");

            $thinkerForm->text("type", "用户类型");

            $list = [];
            if($this->tokenInfo[ConsConfig::$JWT_ACCESS_TYPE] == 0) {
                $list = (new AdminRulesModel())->order("list_order", "desc")->select()->toArray();
            } else {
                $integers = (new AdminGroupAccessModel()).findUserRuleIds($this->tokenInfo);
                $list = (new AdminRulesModel())->whereIn("id", $integers)
                    ->order("list_order", "desc")->select()->toArray();
            }

            $thinkerForm->tree("rules", "使用规则")->setShowCheckbox(true)->setData(
                ThinkerAdmin::tree()->setItemEach(function ($value){
                    return [
                        'text' => $value['title'],
                        'value' => $value['id'],
                        'id' => $value['id'],
                        'pid' => $value['pid']
                    ];
                })->tree($list)
            );

        })->submit("/restful/thinkeradmin/TeAdminRoles", $id)->send();
    }

    /**
     * @title users
     * @description 后台用户管理
     * @author YangYuanCe
     * @return {@link String}
     **/
    public function users() {
        return ThinkerAdmin::table(function($thinkerTable) {
            $thinkerTable
                ->restful("/restful/thinkeradmin/TeAdminUsers")
                ->setOperateUrl("/thinkeradmin/system/usersEdit");

            $thinkerTable->search(function(ThinkerInline $thinkerInline) {
                $thinkerInline->text("username", "用户名称");

                $thinkerInline->text("phone", "手机号");

                $thinkerInline->text("realname", "展示姓名");

                $thinkerInline->select("status", "角色状态")->setPlaceholder()->options([
                    ['text' => "可使用", 'value' => 1],
                    ['text' => "已禁止", 'value' => 0],
                ]);
            });

            $thinkerTable->checkbox();

            $thinkerTable->columns("id", "ID")->setSort(true);

            $thinkerTable->columns("username", "用户名称");

            $thinkerTable->columns("phone", "手机号");

            $thinkerTable->columns("realname", "展示姓名");

            $thinkerTable->columns("status", "角色状态")->switchs("status");

            $this->renderTableRule($thinkerTable);

        })->send("用户管理");
    }

    /**
     * @title usersEdit
     * @description 用户编辑
     * @author YangYuanCe
     * @param id
     * @return {@link String}
     **/
    public function usersEdit($id = 0) {
        return ThinkerAdmin::form(function(ThinkerForm $thinkerForm) use($id) {
            if(!empty($id)) {
                $thinkerForm->setUseValue(AdminMemberModel::get(intval($id))->toArray());
            }

            $thinkerForm->text("username", "用户名称");

            $thinkerForm->text("phone", "手机号");

            $thinkerForm->text("realname", "展示姓名");

            $thinkerForm->password("password", "密码(不修改可不填写)")->setValue("");

            $thinkerForm->switchs("status", "状态");

            //找到所有用户组
            $adminGroups = (new AdminGroupModel())
                ->field("id as value,id,title as text")
                ->where("status", "=", 1)
                ->select()->toArray();


            //找到该用户的所有用户组
            $userCanAccess = [];
            if(!empty($id)) {
                $userCanAccess = array_map(function ($value) {
                    return $value['id'];
                }, (new AdminGroupAccessModel())->findUserGroup([
                    'id' => intval($id),
                    'access_type' => 0
                ]));
            }

            $thinkerForm->tree("groups", "归属用户组")
                ->setShowCheckbox(true)
                ->setData($adminGroups)
                ->setValue(join(",", $userCanAccess));

        })->submit("/restful/thinkeradmin/TeAdminUsers", $id)->send();
    }
}