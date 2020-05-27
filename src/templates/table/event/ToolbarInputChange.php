<?php


namespace Yirius\Admin\templates\table\event;


use Yirius\Admin\templates\Templates;

class ToolbarInputChange extends Templates
{
    protected $args = ["idName", "id", "parseData", "afterReload"];

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
$(document).off("change", "#{$this->getConfig("idName")}")
    .on("change", "#{$this->getConfig("idName")}", function(e){
        layui.excel.importExcel(this.files, {}, function(data){
            //找到第一个Sheet
            var useSheet = [];
            for(var i in data[0]){
                useSheet = data[0][i];
                break;
            }
            var parseData = '{$this->getConfig("parseData")}';
            if(parseData) useSheet = (new Function('return '+parseData))()(useSheet);
            layui.tableplus.reload('{$this->getConfig("id")}', {
                data: useSheet,
                limit: useSheet.length,
                limits: [useSheet.length]
            });
            {$this->getConfig("afterReload")}
        });
    });
HTML;
    }

}