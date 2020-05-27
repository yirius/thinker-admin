<?php


namespace Yirius\Admin\templates\table\event;


use Yirius\Admin\templates\Templates;

class VerifyJs extends Templates
{
    protected $args = ["title", "url", "method", "promptConfig", "sendData", "beforeDelete", "afterDelete"];

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
parent.layer.confirm(layui.laytpl('{$this->getConfig("title")}').render(typeof checkStatus != 'undefined' ? checkStatus : (obj.data || {})), function(index) {
    parent.layer.close(index);
    parent.layer.prompt({$this->getConfig("promptConfig")}, function(value, index){
        parent.layer.close(index);
        var beforeDelete = '{$this->getConfig("beforeDelete")}', sendData = {};
        if(beforeDelete){
            beforeDelete = (new Function('return function(obj, value, checkStatus){' + beforeDelete + '}'))();
        }
        if($.isFunction(beforeDelete)){
            sendData = beforeDelete(obj, value, typeof checkStatus == "undefined" ? {} : checkStatus) || {};
        }
        var url = layui.laytpl('{$this->getConfig("url")}').render(obj.data || {});
        var baseData = {password: layui.md5(value)};if(typeof checkStatus != 'undefined') baseData.data = JSON.stringify(checkStatus.data);
        layui.http['{$this->getConfig("method")}'](url, $.extend(
            baseData, sendData, {$this->getConfig("sendData")}
        ), function(code, msg, data, all){
            layui.admin.reloadTable();
            {$this->getConfig("afterDelete")}
        });
    });
});
HTML;
    }
}