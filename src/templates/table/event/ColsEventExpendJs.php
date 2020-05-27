<?php


namespace Yirius\Admin\templates\table\event;


use Yirius\Admin\templates\Templates;

class ColsEventExpendJs extends Templates
{
    protected $args = ["html"];

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
var _this = $(this), tr = _this.parents('tr'), trIndex = tr.data('index');
if($(this).find("i").hasClass('layui-icon-add-1')){
    $(this).find("i").removeClass('layui-icon-add-1').addClass('layui-icon-fonts-del');
    var tableId = 'tableOut_tableIn_' + trIndex;
    var _html = layui.laytpl('{$this->getConfig("html")}').render(obj.data || {});
    tr.after(_html);
}else{
    $(this).find("i").addClass('layui-icon-add-1').removeClass('layui-icon-fonts-del');
    tr.next().remove();
}
HTML;
    }
}