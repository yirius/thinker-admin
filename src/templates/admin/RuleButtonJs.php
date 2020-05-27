<?php


namespace Yirius\Admin\templates\admin;


use Yirius\Admin\templates\Templates;

class RuleButtonJs extends Templates
{
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
$("#popup_thinker_rule").off("click").on("click", function(){
    layui.view.popup({
        title: '快捷添加界面',
        area: ['80%','80%'],
        id: 'rules_popup',
        success: function(layero, index){
            var container = $("#" + this.id);
            parent.layui.view.fetchHtml('/thinkeradmin/system/rulesEdit', function(res){
                container.html(res.html);
                parent.layui.view.parseHtml(container);
                layui.element.render('breadcrumb', 'thinker-breadcrumb');
            });
        }
    });
});
HTML;
    }

}