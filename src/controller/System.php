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
    public function member()
    {
        return \Yirius\Admin\Admin::table("thinker_admin_member", function(Table $table){

            $table->setRestfulUrl("/restful/adminmember")->setEditPath("/thinkersystem/memberEdit");

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
     * @title memberEdit
     * @description
     * @createtime 2019/2/27 下午2:15
     */
    public function memberEdit()
    {

    }

    public function roles()
    {

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

            $table->setRestfulUrl("/restful/adminrule")->setEditPath("/thinkersystem/rulesEdit");

            $table->columns('', '')->setType('checkbox');

            $table->columns("id", "编号");

            $table->columns('name', "英文名称");

            $table->columns('title', "中文名称");

            $table->columns('jump', "跳转地址");

            $table->columns('sort', "排序");

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
        $value = $id === 0 ? [] : AdminRule::get(['id' => $id])->toArray();

        return \Yirius\Admin\Admin::form("thinkeradmin_admin_rulesedit", function(Form $form) use($value){

            $form->setValue($value);

            $form->text("name", "规则名称(英文)");

            $form->text("title", "中文名称");

            $form->switchs("status", "状态");

            $form->textarea("condition", "逻辑判断");

            $form->text("mid", "上级编号");

            $form->footer()->submit("/restful/adminrule");

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
        return \Yirius\Admin\Admin::form("thinker_admin_menusedit", function(Form $form){

            $form->text("pid", "上层id");

            $form->text("name", "英文名称");

            $form->text("title", "中文名称");

            $form->text("jump", "跳转网址");

            $form->text("icon", "图标");

            $form->text("sort", "排序");

            $form->footer()->submit("/restful/adminmenu");

        })->show();
    }
}