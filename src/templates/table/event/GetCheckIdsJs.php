<?php


namespace Yirius\Admin\templates\table\event;


use Yirius\Admin\templates\Templates;

class GetCheckIdsJs extends Templates
{
    protected $args = [];

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
var checkStatus = layui.tableplus.checkStatus(obj.config.id);
if(checkStatus.data.length == 0){
    layui.admin.modal.error("您尚未选择任何条目");
    return null;
}
var ids = [];
for(var i in checkStatus.data){
    ids.push(checkStatus.data[i].id);
}
obj.data = {id: ids.join(",")};
HTML;
    }
}