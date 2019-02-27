<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/26
 * Time: 下午12:18
 */

namespace Yirius\Admin\controller;


use Yirius\Admin\table\Table;

class System extends AdminController
{
    public function index()
    {

    }

    public function member()
    {
        return \Yirius\Admin\Admin::table("thinker_admin_member", function(Table $table){

            $table->setUrl("/restful/adminmember");

            $table->columns('id', "编号")->setType('checkbox');

            $table->columns('id', "编号");

            $table->columns("username", "登录账号");

            $table->columns("phone", "登录手机号");

            $table->columns("realname", "名称");

            $table->columns("createtime", "创建时间");

            $table->columns("op", "操作")->setWidth(170)->edit()->delete();

            $table->tool()->edit("/thinkeradmin/index")->delete("/thinkeradmin/index/{{d.id}}");

            $table->toolbar()
                ->add()
                ->eventAdd("/restful/adminmember")
                ->delete()
                ->eventDelete("/restful/adminmember");

        })->show();
    }

    public function roles()
    {

    }

    public function rules()
    {

    }

    public function menus()
    {

    }
}