<?php


namespace Yirius\Admin\templates\admin;


use Yirius\Admin\templates\Templates;

class RuleTreeJs extends Templates
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
if(type == "add"){
    return false;
}else{
    if(type == "update"){
        if(typeof obj.data.value == "undefined") {
            layui.form.val("tree_rules", {id: 0, text: obj.data.text, pid: obj.data.pid, name: "", title: "", status: 1, type: 1, url: "", icon: "", listOrder: 1000});
            layui.iconplus.checkIcon("tree_rules_icon", "", "fontClass");
        } else {
            layui.http.get("/restful/thinkeradmin/TeAdminRules/" + obj.data.value, {}, function(data, msg){
                layui.form.val("tree_rules", data);
                layui.iconplus.checkIcon("tree_rules_icon", data.icon, "fontClass");
            });
        }
    }else if(type == "del"){
        if(obj.data.children && obj.data.children.length != 0){
            layui.alert.error("存在下级规则，无法删除");
        }else{
            parent.layer.prompt({formType: 1,title: '敏感操作，请验证口令'}, function(value, index){
                parent.layer.close(index);
                parent.layer.confirm('是否确认要删除该用户规则？', function(index) {
                    parent.layer.close(index);
                    var url = layui.laytpl("/restful/thinkeradmin/TeAdminRules{{parseInt(d.value)?'/'+d.value:''}}").render(obj.data || {});
                    layui.http.delete(url, {password: layui.md5(value)}, function(res){
                        $(obj.elem).remove();
                    });
                });
            });
        }
    }
    return true;
}
HTML;
    }
}