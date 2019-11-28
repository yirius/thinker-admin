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

    /**
     * @title      http
     * @description HTTP请求记录查看
     * @createtime 2019/11/28 11:09 下午
     * @author     yangyuance
     */
    public function http()
    {
        $this->_logtable("http", "请求");
    }

    /**
     * @title      logs
     * @description 错误以及其他日志查看
     * @createtime 2019/11/28 11:09 下午
     * @author     yangyuance
     */
    public function log()
    {
        $this->_logtable("log", "错误");
    }

    /**
     * @title      _logtable
     * @description 查看Log的基础table
     * @createtime 2019/11/28 11:22 下午
     * @param $runPath
     * @param $title
     * @author     yangyuance
     */
    protected function _logtable($runPath, $title)
    {
        //初始化参数
        $month = $this->request->param("month", null);
        $day = $this->request->param("day", null);
        $page = $this->request->param("page", 1);
        $limit = $this->request->param("limit", 10);

        //为了方便，直接把获取json参数放到同一个界面
        if($this->request->param("type") == "ajax"){
            if(is_null($day)){
                ThinkerAdmin::Send()->table(
                    ThinkerAdmin::File()->getCatelogs(
                        env("runtime_path") . $runPath . (is_null($month)?'':DS.$month),
                        intval($page),
                        intval($limit)
                    )
                );
            }else{
                ThinkerAdmin::Send()->table(
                    ThinkerAdmin::File()->getLogContent(
                        env("runtime_path") . $runPath . DS . $month . DS . $day,
                        intval($page),
                        intval($limit)
                    )
                );
            }
        }

        //如果不存在日期，就需要递进文件夹
        if(is_null($day)){
            ThinkerAdmin::Table(function(ThinkerTable $table) use($month, $runPath){

                //如果存在月份，需要去读取月份的数据
                if(!is_null($month)){
                    $table->setId($table->getId() . "_" . $month);
                }

                $table->restful(
                    "/thinkeradmin/Logs/".$runPath."?type=ajax" . (is_null($month)?'':'&month='.$month)
                );

                $table->columns("name", "名称")->setMinWidth(80);

                $table->columns("isdir", "是否文件夹")->setMinWidth(80);

                $table->columns("size", "大小")->setMinWidth(80)
                    ->setTemplet("<div>{{d.size}}KB</div>");

                $table->columns("update_time", "更新时间")->setMinWidth(120);

                $table->columns("op", "操作")->button(
                    "查看",
                    "/thinkeradmin/Logs/".$runPath."?" .
                    (is_null($month) ? 'month={{d.name}}' : 'month='.$month.'&day={{d.name}}'),
                    "search",
                    "",
                    true
                )->setWidth(85);

                $table->toolbar();

            })->send((is_null($month) ? '' : $month).$title."日志");
        }else{
            //存在日期的
            ThinkerAdmin::Table(function(ThinkerTable $table) use($month, $day, $runPath){

                $table->setId($table->getId() . "_" . $month . "_" . str_replace(".log", "", $day));

                $table->restful(
                    "/thinkeradmin/Logs/".$runPath."?type=ajax&month=$month&day=$day"
                );

                $table->columns("id", "ID")->setWidth(60);

                $table->columns("host", "网址")->setWidth(120);

                $table->columns("uri", "网址参数")->setMinWidth(80);

                $table->columns("method", "请求方法")->setWidth(80);

                if($runPath == "log"){
                    $table->columns("error", "错误原因")->setMinWidth(120);

                    $table->columns("memory", "使用内存")->setWidth(80);

                    $table->columns("reqs", "请求数量")->setWidth(80);

                    $table->columns("runtime", "运行时间")->setWidth(80);

                    $table->columns("stack", "堆栈")
                        ->setMinWidth(120)->setTemplet("<div>{{ JSON.stringify(d.stack)}}</div>");
                }else{
                    $table->columns("info", "参数")->setMinWidth(120);
                }

                $table->columns("ip", "访问IP")->setWidth(80);

                $table->columns("timestamp", "访问时间")->setMinWidth(80);

                $table->toolbar();

            })->send($day.$title."日志");
        }
    }
}