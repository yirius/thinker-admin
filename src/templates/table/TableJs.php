<?php


namespace Yirius\Admin\templates\table;


use Yirius\Admin\templates\Templates;

/**
 * Class Tablejs
 * @package Yirius\Admin\templates\table
 */
class TableJs extends Templates
{
    protected $args = ["id", "columns", "jsonConfig"];

    /**
     * @title      render
     * @description
     * @createtime 2020/5/26 9:18 下午
     * @return string
     * @author     yangyuance
     */
    public function render()
    {
        return <<<HTML
;(function(){
    if(!window.tableSearch) window.tableSearch = {};
    var idStr = '{$this->getConfig("id")}';
    window.tableSearch[idStr] = {};
    layui.form.on('submit('+idStr+'_form_search)', function (data) {
        window.tableSearch[idStr] = data.field;
        //执行重载
        layui.tableplus.reload(idStr, {
            where: window.tableSearch[idStr]
        });
        return false;
    });
    window["_"+idStr+"_ins"] = layui.tableplus.init(idStr, $.extend({
        response: {
            statusName: layui.conf.response.statusName,
            statusCode: layui.conf.response.statusCode.ok,
            msgName: layui.conf.response.msgName,
            countName: layui.conf.response.countName,
            dataName: layui.conf.response.dataName
        },
        cols: [{$this->getConfig("columns")}]
    }, layui.conf.table.config, {$this->getConfig("jsonConfig")} ));
})();
HTML;
    }
}