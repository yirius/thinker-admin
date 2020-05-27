<?php


namespace Yirius\Admin\templates\table\event;


use Yirius\Admin\templates\Templates;

class PopupJs extends Templates
{
    protected $args = ["view", "id", "title", "data", "area"];

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
layui.view.popup({
    title: layui.laytpl('{$this->getConfig("title")}').render($.extend(obj.data || {}, {$this->getConfig("data")})),
    area: {$this->getConfig("area")},
    id: '{$this->getConfig("id")}',
    success: function(layero, index){
        var container = $("#" + this.id);
        parent.layui.view.fetchHtml(layui.laytpl('{$this->getConfig("view")}').render($.extend(obj.data || {}, {$this->getConfig("data")})), function(res){
            container.html(res.html);
            parent.layui.view.parseHtml(container);
            layui.element.render('breadcrumb', 'thinker-breadcrumb');
        });
    }
});
HTML;
    }
}