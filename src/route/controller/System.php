<?php


namespace Yirius\Admin\route\controller;


use Yirius\Admin\form\assemblys\Button;
use Yirius\Admin\form\assemblys\Tree;
use Yirius\Admin\form\assemblys\TreePlus;
use Yirius\Admin\form\ThinkerForm;
use Yirius\Admin\layout\ThinkerCard;
use Yirius\Admin\layout\ThinkerPage;
use Yirius\Admin\route\model\TeAdminRoles;
use Yirius\Admin\route\model\TeAdminRolesAccess;
use Yirius\Admin\route\model\TeAdminRules;
use Yirius\Admin\route\model\TeAdminUsers;
use Yirius\Admin\table\ThinkerTable;
use Yirius\Admin\ThinkerAdmin;

class System
{
    /**
     * @title      getRuleTree
     * @description 获取到规则树
     * @createtime 2019/11/20 2:35 下午
     * @return ThinkerCard
     * @author     yangyuance
     */
    protected function getRuleTree()
    {
        //序列化所有的菜单
        $treeData = ThinkerAdmin::Tree()
            ->setConfig([
                'sublist' => "children"
            ])
            ->tree(TeAdminRules::select()->toArray());

        if(isset($treeData[0])){
            $treeData[0]['spread'] = true;
        }

        //点击打开快捷添加界面
        ThinkerAdmin::script(<<<HTML
$("#popup_thinker_rule").off("click").on("click", function(){
    layui.view.popup({
        title: '快捷添加界面',
        area: ['80%','80%'],
        id: 'rules_popup',
        success: function(layero, index){
            var container = $("#" + this.id);
            parent.layui.view.loadHtml('/thinkeradmin/System/rulesEdit', function(res){
                container.html(res.html);
                parent.layui.view.parse(container);
                layui.element.render('breadcrumb', 'thinker-breadcrumb');
            });
        }
    });
});
HTML
        );

        return (new ThinkerCard())->setCardClass("thinker-pad10", "header")->setHeaderLayout(
            (new Button())->sm()->setText("快捷添加界面逻辑")->setId("popup_thinker_rule")->render()
        )->setBodyLayout(
            (new TreePlus("tree"))
                ->setData($treeData)
                ->setEdit(['add', 'update', 'del'])
                ->setBeforeOperateEvent(<<<HTML
if(type == "add"){
    return false;
}else{
    if(type == "update"){
        layui.form.val("tree_rules", obj.data);
        layui.iconplus.checkIcon("tree_rules_icon", obj.data.icon,"layui_icon");
    }else if(type == "del"){
        if(obj.data.children && obj.data.children.length != 0){
            layui.admin.modal.error("存在下级规则，无法删除");
        }else{
            parent.layer.prompt({formType: 1,title: '敏感操作，请验证口令'}, function(value, index){
                parent.layer.close(index);
                parent.layer.confirm('是否确认要删除该用户规则？', function(index) {
                    parent.layer.close(index);
                    var url = layui.laytpl("/restful/thinkeradmin/TeAdminRules{{parseInt(d.id)?'/'+d.id:''}}").render(obj.data || {});
                    layui.admin.http.delete(url, {password: value}, function(res){
                        $(obj.elem).remove();
                    });
                });
            });
        }
    }
    return true;
}
HTML
                )
        );
    }

    /**
     * @title      getRulesForm
     * @description 获取到提交的右侧界面
     * @createtime 2019/11/20 2:41 下午
     * @return string
     * @author     yangyuance
     */
    protected function getRulesForm()
    {
        return ThinkerAdmin::Form(function(ThinkerForm $form){
            $form->setId("tree_rules");

            $form->hidden("id", "")->setValue(0);

            $form->text("pid", "上级编号");

            $form->text("name", "规则英文");

            $form->text("title", "规则名称");

            $form->switchs("status", "规则状态");

            $form->select("type", "规则类型")->options([
                ['text' => "菜单栏目", 'value' => 1],
                ['text' => "非菜单界面", 'value' => 2],
                ['text' => "界面权限", 'value' => 3],
            ]);

            $form->text("url", "对应网址");

            $form->iconpicker("icon", "对应图标");

            $form->text("list_order", "规则排序");

        })->submit(
            "/restful/thinkeradmin/TeAdminRules{{parseInt(d.id)?'/'+d.id:''}}",
            true, null, <<<HTML
function(obj, url){
    return {
        method: parseInt(obj.field.id) ? 'put' : 'post'
    };
}
HTML
        )->render();
    }

    /**
     * @title      rules
     * @description
     * @createtime 2019/11/15 6:47 下午
     * @author     yangyuance
     */
    public function rules()
    {
        ThinkerAdmin::send()->html(
            (new ThinkerPage(function(ThinkerPage $page){
                $rows = $page->rows()->space(10);
                $rows->cols()->sm(7)->layout(
                    $this->getRuleTree()
                );
                $rows->cols()->sm(5)->layout(
                    (new ThinkerCard())->setBodyLayout(
                        $this->getRulesForm()
                    )
                );
            }))->setTitle("规则管理")->render()
        );
    }

    /**
     * @title      rulesEdit
     * @description
     * @createtime 2019/11/16 10:28 下午
     * @param int $id
     * @author     yangyuance
     */
    public function rulesEdit()
    {
        ThinkerAdmin::Form(function(ThinkerForm $form){
            $form->select("pid", "上级编号")->options(
                TeAdminRules::adminSelect()
                    ->setWhere([
                        ['type', 'in', [1,2]]
                    ])
                    ->getResult()
            );

            $form->text("name", "规则英文");

            $form->text("title", "规则名称");

            $form->select("type", "规则类型")->options([
                ['text' => "菜单栏目", 'value' => 1],
                ['text' => "非菜单界面", 'value' => 2]
            ]);

            $form->text("url", "对应网址");

            $form->text("restfulurl", "对应restful网址");

            $form->switchs("opurl", "对应操作编辑网址");

            $form->checkbox("tableconf[]", "界面设置")->options([
                ['text' => "添加", 'value' => "add"],
                ['text' => "删除", 'value' => "del"],
                ['text' => "修改", 'value' => "edit"],
                ['text' => "导出", 'value' => "exports"],
            ]);

            $form->text("list_order", "规则排序");

        })->submit("/restful/thinkeradmin/TeAdminRules?__type=quickadd")->send("快捷添加界面");
    }

    /**
     * @title      roles
     * @description
     * @createtime 2019/11/25 5:25 下午
     * @author     yangyuance
     */
    public function roles()
    {
        ThinkerAdmin::Table(function(ThinkerTable $table){
            $table->restful("/restful/thinkeradmin/TeAdminRoles")
                ->setOperateUrl("/thinkeradmin/System/rolesEdit");

            $table->columns("id", "ID");

            $table->columns("title", "角色名称");

            $table->columns("status", "角色状态")->switchs("status");

            $table->columns("rules", "规则数量")
                ->setTemplet("<div>{{d.rules.split(',').length}}种</div>");

            $table->columns("op", "操作")->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

            $table->colsEvent()->edit()->delete();

        })->send("角色管理");
    }

    /**
     * @title      rolesEdit
     * @description
     * @createtime 2019/11/25 5:32 下午
     * @param int $id
     * @author     yangyuance
     */
    public function rolesEdit($id = 0)
    {
        ThinkerAdmin::Form(function(ThinkerForm $form) use($id){

            $value = $id == 0 ? [] : TeAdminRoles::get(['id' => $id]);

            $form->setValue($value);

            $form->text("title", "规则名称");

            $form->switchs("status", "状态");

            //序列化所有的菜单
            $useRules = empty($value['rules']) ? [] : explode(",", $value['rules']);
            $treeData = ThinkerAdmin::Tree()
                ->setConfig([
                    'sublist' => "children"
                ])
                ->setItemEach(functioN($value) use($useRules){
                    if(in_array($value['id'], $useRules)){
                        $value['checked'] = true;
                    }
                    return $value;
                })
                ->tree(TeAdminRules::select()->toArray());

            $form->tree("rules", "使用规则")->setData($treeData)->setShowCheckbox(true);

        })->submit("/restful/thinkeradmin/TeAdminRoles", $id)->send("角色编辑");
    }

    /**
     * @title      users
     * @description
     * @createtime 2019/11/25 5:57 下午
     * @author     yangyuance
     */
    public function users()
    {
        ThinkerAdmin::Table(function(ThinkerTable $table){
            $table->restful("/restful/thinkeradmin/TeAdminUsers")
                ->setOperateUrl("/thinkeradmin/System/usersEdit");

            $table->columns("id", "ID");

            $table->columns("username", "用户名称");

            $table->columns("phone", "手机号");

            $table->columns("realname", "真实姓名");

            $table->columns("status", "角色状态")->switchs("status");

            $table->columns("op", "操作")->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

            $table->colsEvent()->edit()->delete();

        })->send("角色管理");
    }

    /**
     * @title      rolesEdit
     * @description
     * @createtime 2019/11/25 5:32 下午
     * @param int $id
     * @author     yangyuance
     */
    public function usersEdit($id = 0)
    {
        ThinkerAdmin::Form(function(ThinkerForm $form) use($id){

            $value = $id == 0 ? [] : TeAdminUsers::get(['id' => $id]);

            $form->setValue($value);

            $form->text("username", "用户名称");

            $form->text("phone", "手机号");

            $form->text("realname", "展示姓名");

            $form->password("password", "密码(不修改可不填写)")->setValue('');

            $form->switchs("status", "状态");

            //找到所有的角色
            $roles = TeAdminRoles::adminSelect()->setWhere([
                ['status', '=', 1]
            ])->getResult();
            //找到当前使用角色
            $useRoles = $id == 0 ? [] : TeAdminRolesAccess::getAccess($id);
            //构造渲染tree角色
            $treeData = [];
            foreach ($roles as $role){
                $treeData[] = [
                    'title' => $role['text'],
                    'id' => $role['value'],
                    'checked' => in_array($role['value'], $useRoles)
                ];
            }
            $form->tree("groups", "归属用户组")
                ->setData($treeData)
                ->setShowCheckbox(true)
                ->setValue(join(",", $useRoles));

        })->submit("/restful/thinkeradmin/TeAdminUsers", $id)->send("角色编辑");
    }
}