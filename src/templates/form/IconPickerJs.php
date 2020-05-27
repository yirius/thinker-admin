<?php


namespace Yirius\Admin\templates\form;


use Yirius\Admin\templates\Templates;

class IconPickerJs extends Templates
{
    protected $args = ["id", "idName", "clickEvent", "jsonConfig"];

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
layui.iconplus.render($.extend({
    // 选择器，推荐使用input
    elem: '#{$this->getConfig("id")}', //选择器ID
    // 数据类型：fontClass/layui_icon，
    type: 'fontClass',
    // 是否开启搜索：true/false
    search: true,
    // 是否开启分页
    page: true,
    // 每页显示数量，默认12
    limit: 12,
    // 点击回调
    click: function (data) {
        //console.log(data);
        $("#{$this->getConfig("idName")}").val(data.icon);

        {$this->getConfig("clickEvent")}
    }
}, {$this->getConfig("jsonConfig")} ));
HTML;
    }
}