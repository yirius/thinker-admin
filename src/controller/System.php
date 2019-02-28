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
use Yirius\Admin\model\table\AdminMember;
use Yirius\Admin\model\table\AdminMenu;
use Yirius\Admin\model\table\AdminRole;
use Yirius\Admin\model\table\AdminRule;
use Yirius\Admin\table\Table;

class System extends AdminController
{
    public function index()
    {

    }

    /**
     * @title member
     * @description system member list
     * @createtime 2019/2/27 下午2:15
     * @return mixed
     */
    public function members()
    {
        return \Yirius\Admin\Admin::table("thinker_admin_member", function(Table $table){

            $table
                ->setRestfulUrl("/restful/adminmember")
                ->setEditPath("/thinkersystem/membersEdit");

            $table->search(function(Inline $form){
                $form->text("username", "登录账号");

                $form->text("phone", "登录手机号");
            });

            $table->columns('id', "编号")->setType('checkbox');

            $table->columns('id', "编号");

            $table->columns("username", "登录账号");

            $table->columns("phone", "登录手机号");

            $table->columns("realname", "名称");

            $table->columns("createtime", "创建时间");

            $table->columns("op", "操作")->setWidth(170)->edit()->delete();

            $table->tool()->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

        })->show();
    }

    /**
     * @title membersEdit
     * @description
     * @createtime 2019/2/28 下午4:56
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function membersEdit($id = 0)
    {
        return \Yirius\Admin\Admin::form("thinker_admin_members", function(Form $form) use($id){

            $value = $id === 0 ? [] : AdminMember::get(['id' => $id])->toArray();
            unset($value['password']);

            $form->setValue($value);

            $form->text("username", "登录账号");

            $form->text("phone", "登录手机号");

            $form->text("realname", "真实姓名");

            $form->text("password", "密码");

            $form->switchs("status", "状态");

            $form->footer()->submit("/restful/adminmember", $id);

        })->show();
    }

    /**
     * @title roles
     * @description
     * @createtime 2019/2/28 下午4:25
     * @return mixed
     */
    public function roles()
    {
        return \Yirius\Admin\Admin::table("thinker_admin_roles", function(Table $table){

            $table
                ->setRestfulUrl("/restful/adminrole")
                ->setEditPath("/thinkersystem/rolesEdit");

            $table->columns("id", "编号");

            $table->columns("title", "角色名称");

            $table->columns("status", "状态")->setSwitchTemplet("status");

            $table->columns('op', '操作')->edit()->delete();

            $table->tool()->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

        })->show();
    }

    /**
     * @title rolesEdit
     * @description
     * @createtime 2019/2/28 下午4:59
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function rolesEdit($id = 0)
    {
        return \Yirius\Admin\Admin::form("thinker_admin_members", function(Form $form) use($id){

            $form->setValue($id === 0 ? [] : AdminRole::get(['id' => $id])->toArray());

            $form->text("username", "登录账号");

            $form->selectplus("username", "登录账号");

            $form->footer()->submit("/restful/adminrole", $id);

        })->show();
    }

    /**
     * @title rules
     * @description
     * @createtime 2019/2/28 上午11:09
     * @return mixed
     */
    public function rules()
    {
        return \Yirius\Admin\Admin::table("thinker_admin_rules", function(Table $table){

            $table
                ->setRestfulUrl("/restful/adminrule")
                ->setEditPath("/thinkersystem/rulesEdit");

            $table->columns('', '')->setType('checkbox');

            $table->columns("id", "编号");

            $table->columns('name', "英文名称");

            $table->columns('title', "中文名称");

            $table->columns('status', "状态")->setSwitchTemplet('status');

            $table->columns('op', '操作')->edit()->delete();

            $table->tool()->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

            $table->setLimit(10000)->setLimits([10000]);

        })->show();
    }

    /**
     * @title rulesEdit
     * @description
     * @createtime 2019/2/28 上午11:18
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function rulesEdit($id = 0)
    {

        return \Yirius\Admin\Admin::form("thinkeradmin_admin_rulesedit", function(Form $form) use($id){

            $form->setValue($id === 0 ? [] : AdminRule::get(['id' => $id])->toArray());

            $form->text("name", "规则名称(英文)");

            $form->text("title", "中文名称");

            $form->switchs("status", "状态");

            $form->textarea("condition", "逻辑判断");

            $form->text("mid", "上级编号");

            $form->footer()->submit("/restful/adminrule", $id);

        })->show();
    }

    /**
     * @title menus
     * @description
     * @createtime 2019/2/27 下午4:48
     * @return mixed
     */
    public function menus()
    {
        return \Yirius\Admin\Admin::table("thinker_admin_menus", function(Table $table){

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

        })->show();
    }

    /**
     * @title menusEdit
     * @description
     * @createtime 2019/2/27 下午4:49
     * @param int $id
     * @return \Yirius\Admin\form\Form
     * @throws \Exception
     */
    public function menusEdit($id = 0)
    {
        return \Yirius\Admin\Admin::form("thinker_admin_menusedit", function(Form $form) use($id){

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
}