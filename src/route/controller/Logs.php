<?php


namespace Yirius\Admin\route\controller;


use Yirius\Admin\extend\ThinkerController;
use Yirius\Admin\form\ThinkerInline;
use Yirius\Admin\table\ThinkerTable;
use Yirius\Admin\ThinkerAdmin;

class Logs extends ThinkerController
{
    /**
     * @title      _table
     * @description 基础方法
     * @createtime 2019/11/28 7:12 下午
     * @param     $title
     * @param int $isLogin
     * @author     yangyuance
     */
    protected function _table($title, $isLogin = 0)
    {
        ThinkerAdmin::Table(function(ThinkerTable $table) use($isLogin){

            $table->restful("/restful/thinkeradmin/TeAdminLogs?islogin=".$isLogin);

            $table->search(function (ThinkerInline $inline){

                $inline->text("title", "角色名称");

                $inline->select("status", "角色状态")->options([
                    ['text' => "可使用", 'value' => 1],
                    ['text' => "已禁止", 'value' => 0],
                ])->setPlaceholder();

            });

            $table->checkbox();

            $table->columns("id", "ID")->setSort(true)->setWidth(80);

            $table->columns("userid", "用户ID")->setWidth(80);

            $table->columns("user_type", "用户类型")->setWidth(80);

            $table->columns("realname", "展示姓名")->setMinWidth(120);

            $table->columns("desc", "操作描述")->setMinWidth(120);

            $table->columns("funcname", "操作方法")->setMinWidth(120);

            $table->columns("usetime", "操作时间")->setWidth(80);

            $table->columns("requesttype", "请求类型")->setWidth(80);

            $table->columns("params", "请求参数")->setMinWidth(80);

            $table->columns("ip", "ip地址")->setMinWidth(80);

            $table->columns("address", "ip所在地")->setWidth(100);

            $table->columns("create_time", "创建时间")->setMinWidth(80);

            $this->renderTableRule($table);
        })->send($title);
    }

    /**
     * @title      system
     * @description 系统日志显示
     * @createtime 2019/11/28 7:12 下午
     * @author     yangyuance
     */
    public function system()
    {
        $this->_table("系统日志");
    }

    /**
     * @title      login
     * @description 登录日志显示
     * @createtime 2019/11/28 7:13 下午
     * @author     yangyuance
     */
    public function login()
    {
        $this->_table("登录日志", 1);
    }

    public function http()
    {

    }

    public function logs()
    {

    }
}