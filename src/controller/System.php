<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/26
 * Time: 下午12:18
 */

namespace Yirius\Admin\controller;


use Yirius\Admin\form\Form;
use Yirius\Admin\form\Inline;
use Yirius\Admin\form\Tab;
use Yirius\Admin\layout\PageView;
use Yirius\Admin\model\table\AdminMember;
use Yirius\Admin\model\table\AdminMenu;
use Yirius\Admin\model\table\AdminRole;
use Yirius\Admin\model\table\AdminRoleAccess;
use Yirius\Admin\model\table\AdminRule;
use Yirius\Admin\table\Table;

class System extends AdminController
{
    /**
     * @title member
     * @description system member list
     * @create_time 2019/2/27 下午2:15
     * @return mixed
     */
    public function members()
    {
        return \Yirius\Admin\Admin::table("thinker_admin_member", function (Table $table) {

            $table
                ->setRestfulUrl("/restful/adminmember")
                ->setEditPath("/thinkersystem/membersEdit");

            $table->search(function (Inline $form) {
                $form->text("username", "登录账号");

                $form->text("phone", "登录手机号");
            });

            $table->columns('id', "编号")->setType('checkbox');

            $table->columns('id', "编号");

            $table->columns("username", "登录账号");

            $table->columns("phone", "登录手机号");

            $table->columns("realname", "名称");

            $table->columns("create_time", "创建时间");

            $table->columns("op", "操作")->setWidth(170)->edit()->delete();

            $table->tool()->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

        })->show(function(PageView $pageView, $render, $header){
            $pageView->breadcrumb([
                ['text' => "Admin管理"],
                ['text' => "Members管理"],
            ]);
            $pageView->card($render, $header);
        });
    }

    /**
     * @title membersEdit
     * @description
     * @create_time 2019/2/28 下午4:56
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function membersEdit($id = 0)
    {
        return \Yirius\Admin\Admin::form("thinker_admin_members", function (Form $form) use ($id) {

            if($id === 0){
                $value = [];
            }else{
                $value = AdminMember::get(['id' => $id])->toArray();
                unset($value['password']);
                //check if there have group_access data
                $groups = AdminRoleAccess::field("group_id")->where([
                    ['uid', '=', $id],
                    ['type', '=', 0]
                ])->select()->toArray();
                $value['groups[]'] = [];
                foreach($groups as $i => $v){
                    $value['groups[]'][] = $v['group_id'];
                }
            }

            $form->setValue($value);

            $form->text("username", "登录账号");

            $form->text("phone", "登录手机号");

            $form->text("realname", "真实姓名");

            $form->text("password", "密码");

            $form->switchs("status", "状态");

            $form->checkbox("groups[]", "角色权限")
                ->options(
                    AdminRole::adminSelect()->setWhere([
                        ['status', '=', 1]
                    ])->getResult()
                )
                ->primary();

            $form->footer()->submit("/restful/adminmember", $id);

        })->show();
    }

    /**
     * @title roles
     * @description
     * @create_time 2019/2/28 下午4:25
     * @return mixed
     */
    public function roles()
    {
        return \Yirius\Admin\Admin::table("thinker_admin_roles", function (Table $table) {

            $table
                ->setRestfulUrl("/restful/adminrole")
                ->setEditPath("/thinkersystem/rolesEdit");

            $table->search(function(Inline $inline){

                $inline->text("title", "角色名称");

                $inline->select("status", "状态")->options([
                    ['text' => "开启", 'value' => 1],
                    ['text' => "关闭", 'value' => 0],
                ])->setPlaceholder("--全部--");
            });

            $table->columns("id", "编号");

            $table->columns("title", "角色名称");

            $table->columns("status", "状态")->setSwitchTemplet("status");

            $table->columns('op', '操作')->edit()->delete();

            $table->tool()->edit()->delete();

            $table->toolbar()->add()->delete()
                ->button("清除缓存", "clearcache", "app", "layui-btn-normal")
                ->event()->add()->delete()
                ->event("clearcache", <<<HTML
layer.prompt({formType: 1,title: '敏感操作，请验证口令'}, function(value, index){
    layer.close(index);
    layer.confirm('确定删除吗？', function(index) {
        layer.close(index);
        layui.http.request({
            url: "/restful/adminrole/1", 
            data: layui.http._beforeAjax({password: value})
        });
    });
});
HTML
                );

        })->show(function(PageView $pageView, $render, $header){
            $pageView->breadcrumb([
                ['text' => "Admin管理"],
                ['text' => "Roles管理"],
            ]);
            $pageView->card($render, $header);
        });
    }

    /**
     * @title rolesEdit
     * @description
     * @create_time 2019/2/28 下午4:59
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function rolesEdit($id = 0)
    {
        return \Yirius\Admin\Admin::form("thinker_admin_members", function (Form $form) use ($id) {

            $form->setValue($id === 0 ? [] : AdminRole::get(['id' => $id])->toArray());

            $form->text("title", "角色名称");

            $rulesTree = \Yirius\Admin\Admin::tools()->tree(AdminRule::all()->toArray(),
                function ($data) {
                    return [
                        'title' => $data['title'],
                        'id' => $data['id']
                    ];
                }, [
                    'parentid' => "mid",
                    'sublist' => "children"
                ]
            );

            $form->tree("rules", "使用规则")
                ->setData($rulesTree)
                ->setShowCheckbox(true);

            $form->switchs("status", "状态");

            $form->footer()->submit("/restful/adminrole", $id);

        })->show();
    }

    /**
     * @title rules
     * @description
     * @create_time 2019/2/28 上午11:09
     * @return mixed
     */
    public function rules()
    {
        return \Yirius\Admin\Admin::table("thinker_admin_rules", function (Table $table) {

            $table
                ->setRestfulUrl("/restful/adminrule")
                ->setEditPath("/thinkersystem/rulesEdit");

            $table->search(function(Inline $inline){
                //judge if there have user's type
                $ruleTypes = config("thinkeradmin.rule.type");
                $inline->select("type", "类型")->options(array_merge([
                    ['text' => "路由规则", 'value' => 1],
                    ['text' => "界面规则", 'value' => 2]
                ], empty($ruleTypes) ? [] : $ruleTypes))->setPlaceholder("--全部--");
            });

            $table->columns('', '')->setType('checkbox');

            $table->columns("id", "编号");

            $table->columns('name', "英文名称");

            $table->columns('title', "中文名称");

            $table->columns('status', "状态")->setSwitchTemplet('status');

            $table->columns('op', '操作')->edit()->delete();

            $table->tool()->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

            $table->setLimit(100000)->setLimits([100000]);

        })->show(function(PageView $pageView, $render, $header){
            $pageView->breadcrumb([
                ['text' => "Admin管理"],
                ['text' => "Rules管理"],
            ]);
            $pageView->card($render, $header);
        });
    }

    /**
     * @title rulesEdit
     * @description
     * @create_time 2019/2/28 上午11:18
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function rulesEdit($id = 0)
    {

        return \Yirius\Admin\Admin::form("thinkeradmin_admin_rulesedit", function (Form $form) use ($id) {

            $form->setValue($id === 0 ? [] : AdminRule::get(['id' => $id])->toArray());

            $form->text("name", "规则名称(英文)");

            $form->text("title", "中文名称");

            $form->switchs("status", "状态");

            $form->textarea("condition", "逻辑判断");

            $form->text("mid", "上级编号");

            //judge if there have user's type
            $ruleTypes = config("thinkeradmin.rule.type");
            $form->select("type", "类型")->options(array_merge([
                ['text' => "路由规则", 'value' => 1],
                ['text' => "界面规则", 'value' => 2]
            ], empty($ruleTypes) ? [] : $ruleTypes));

            $form->footer()->submit("/restful/adminrule", $id);

        })->show();
    }

    /**
     * @title menus
     * @description
     * @create_time 2019/2/27 下午4:48
     * @return mixed
     */
    public function menus()
    {
        return \Yirius\Admin\Admin::table("thinker_admin_menus", function (Table $table) {

            $table->setRestfulUrl("/restful/adminmenu")->setEditPath("/thinkersystem/menusEdit");

            $table->columns('', '')->setType('checkbox');

            $table->columns("id", "编号");

            $table->columns('name', "英文名称");

            $table->columns('title', "中文名称");

            $table->columns('jump', "跳转地址");

            $table->columns('sort', "排序");

            $table->columns('op', '操作')->edit()->delete();

            $table->tool()->edit()->delete();

            $table->toolbar()->add()->delete()
                ->event()->add()->delete();

            $table->setLimit(100000)->setLimits([100000]);
        })->show(function(PageView $pageView, $render, $header){
            $pageView->breadcrumb([
                ['text' => "Admin管理"],
                ['text' => "Menus管理"],
            ]);
            $pageView->card($render, $header);
        });
    }

    /**
     * @title menusEdit
     * @description
     * @create_time 2019/2/27 下午4:49
     * @param int $id
     * @return \Yirius\Admin\form\Form
     * @throws \Exception
     */
    public function menusEdit($id = 0)
    {
        return \Yirius\Admin\Admin::form("thinker_admin_menusedit", function (Form $form) use ($id) {

            $form->setValue($id === 0 ? [] : AdminMenu::get(['id' => $id])->toArray());

            $form->text("pid", "上层id");

            $form->text("name", "英文名称");

            $form->text("title", "中文名称");

            $form->text("jump", "跳转网址");

            $form->text("icon", "图标");

            $form->text("sort", "排序");

            $form->footer()->submit("/restful/adminmenu", $id);

        })->show();
    }

    /**
     * @title configs
     * @description
     * @createtime 2019/3/26 下午1:52
     * @return mixed
     * @throws \Exception
     */
    public function configs()
    {
        return \Yirius\Admin\Admin::form("thinker_admin_configs", function(Form $form){

            $configs = \Yirius\Admin\model\table\AdminConfigs::all()->toArray();
            $configValues = [];
            foreach($configs as $i => $v){
                $configValues[$v['name']] = $v['value'];
            }
            $form->setValue($configValues);

            $form->tab("网站设置", function (Tab $tab){

                $tab->switchs("isclosed", "关闭网站");

                $tab->text("title", "网站名称");

                $tab->upload("logo", "网站LOGO");

                $tab->upload("ico", "地址栏ICO");

                $tab->text("seo_title", "SEO标题");

                $tab->text("seo_keywords", "SEO关键词");

                $tab->textarea("seo_description", "SEO描述");

                $tab->text("copyright", "版权信息");

                $tab->text("beian", "备案号");

            });

            $form->tab("基础设置", function (Tab $tab){

                $tab->switchs("isdebug", "是否DEBUG");

                $tab->switchs("openwidgets", "开启插件");

                $tab->switchs("istrace", "显示TRACE");

                $tab->text("errortpl", "错误模板");

                $tab->switchs("isssl", "启用HTTPS");

            });

            $form->footer()->submit("/restful/adminconfigs");

//            $form->tab("上传设置", function (Tab $tab){
//
//                $tab->text("images_size", "图片大小/B");
//
//                $tab->textarea("images_ext", "图片类型");
//
//                $tab->text("files_size", "文件大小/B");
//
//                $tab->textarea("files_ext", "文件类型");
//
//            });
//
//            $form->tab("水印设置", function (Tab $tab){
//
//                $tab->switchs("iswater", "开启水印");
//
//                $tab->switchs("watertype", "水印类型")->text("文字", "图片");
//
//            });

        })->show();
    }
}