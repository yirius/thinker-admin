<?php


namespace Yirius\Admin\templates\table\event;


use Yirius\Admin\templates\Templates;

class ToolbarSubmitXlsxJs extends Templates
{
    protected $args = ["method", "url", "sendData", "afterRequest", "errorRequest"];

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
var resultData = layui.tableplus.cache[obj.config.id];
if(resultData.length == 0){
    layui.admin.modal.error("您尚未导入excel");
    return null;
}
layer.confirm('是否确定导入'+ resultData.length +'条数据？', function(index) {
    parent.layer.close(index);
    layui.http['{$this->getConfig("method")}']('{$this->getConfig("url")}', $.extend({data: JSON.stringify(resultData)}, {$this->getConfig("sendData")}), function(data, msg){
        {$this->getConfig("afterRequest")}
    }, function(code, msg, data, all){
        {$this->getConfig("errorRequest")}
    });
});
HTML;
    }

}