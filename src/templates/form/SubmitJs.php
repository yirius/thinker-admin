<?php


namespace Yirius\Admin\templates\form;


use Yirius\Admin\templates\Templates;

class SubmitJs extends Templates
{
    protected $args = ["idName", "url", "requestMethod", "beforeEvent", "doneCall"];

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
layui.form.on("submit({$this->getConfig("idName")})", function (obj) {
    //找到switch，然后给其赋值
    var switchs = $(obj.form).find('input[lay-skin="switch"]');
    layui.each(switchs, function(n,v){
        if(!v.checked){
            obj.field[v.name] = v.dataset.notuse;
        }
    });
    
    //判断tree的参数，去掉
    var treeChecks = $(obj.form).find('input[name^="layuiTreeCheck_"]');
    layui.each(treeChecks, function(n,v){
        if(obj.field[v.name]){
            delete obj.field[v.name];
        }
    });
    
    //判断富文本编辑器
    $(obj.form).find("[lay-tinymce]").each(function(n, v){
        if(tinyMCE.editors[v.id]) {
            obj.field[v.name] = tinyMCE.editors[v.id].getContent();
        }
    });

    try{
        var beforeEvent = '{$this->getConfig("beforeEvent")}',
            requestMethod = '{$this->getConfig("requestMethod")}',
            sendData = obj.field || {},
            url = layui.laytpl("{$this->getConfig("url")}").render(sendData);
        if(beforeEvent){
            beforeEvent = new Function('return ' + beforeEvent)();
            var returnData = beforeEvent(obj, url);
            if(returnData.data){
                sendData = returnData.data;
            }
            if(returnData.url){
                url = returnData.url;
            }
            if(returnData.method){
                requestMethod = returnData.method;
            }
        }
        layui.http[requestMethod](url, sendData, function(data, msg){
            {$this->getConfig("doneCall")}
        });
    }catch(e){
        console.error(e);
    }
    return false;
});
HTML;
    }
}