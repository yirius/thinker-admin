<?php


namespace Yirius\Admin\templates\table\columns;


use Yirius\Admin\templates\Templates;

class SwitchsJs extends Templates
{
    protected $args = ["url", "config", "beforePut", "afterPut"];

    /**
     * @title      render
     * @description
     * @createtime 2020/5/26 9:18 下午
     * @return string
     * @author     yangyuance
     */
    public function render()
    {
        $config = $this->getConfig("config");

        return <<<HTML
layui.form.on('switch(switch{$config["filter"]})', function(obj){
    var renderData = JSON.parse(obj.elem.dataset.json),
        beforePut = '{$this->getConfig("beforePut")}',
        afterPut = '{$this->getConfig("afterPut")}';
    if(beforePut) renderData = (new Function('return '+beforePut))()(renderData);
    layui.http.put(layui.laytpl("{$this->getConfig("url")}").render(renderData), {
        value: obj.elem.checked ? '{$config["checkedValue"]}' : '{$config["unCheckedValue"]}',
        field: '{$config["filter"]}'
    }, function(data, msg){
        if(afterPut) (new Function('return '+afterPut))()(code,msg,data,all);
    });
});
HTML;
    }
}