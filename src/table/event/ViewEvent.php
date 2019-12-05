<?php


namespace Yirius\Admin\table\event;


use Yirius\Admin\extend\ThinkerLayout;

abstract class ViewEvent extends ThinkerLayout
{
    /**
     * @title      _popup
     * @description
     * @createtime 2019/11/15 6:03 下午
     * @param       $view
     * @param       $title
     * @param array $config
     * @return string
     * @author     yangyuance
     */
    public function _popup($view, $title, array $config = [])
    {
        $config = array_merge([
            'area' => ['80%', '80%'],
            'id' => null,
            'data' => []
        ], $config);

        if(empty($config['id'])) $config['id'] = "popup_".time();
        
        $area = json_encode($config['area']);
        $data = json_encode($config['data']);

        return <<<HTML
layui.view.popup({
    title: layui.laytpl('{$title}').render($.extend(obj.data || {}, {$data})),
    area: {$area},
    id: '{$config['id']}',
    success: function(layero, index){
        var container = $("#" + this.id);
        parent.layui.view.loadHtml(layui.laytpl('{$view}').render($.extend(obj.data || {}, {$data})), function(res){
            container.html(res.html);
            parent.layui.view.parse(container);
            layui.element.render('breadcrumb', 'thinker-breadcrumb');
        });
    }
});
HTML
            ;
    }

    /**
     * @title      _multiverfiy
     * @description
     * @createtime 2019/11/15 6:24 下午
     * @param        $title
     * @param        $url
     * @param array  $sendData
     * @param string $method
     * @param string $afterDelete
     * @return string
     * @author     yangyuance
     */
    public function _multiverfiy($title, $url, array $sendData = [], $method = "delete", $afterDelete = '', $promptConfig = [])
    {
        $sendData = json_encode($sendData);

        if(empty($promptConfig)) $promptConfig = ['formType' => 1, 'title' => "敏感操作，请验证口令"];

        $promptConfig = json_encode($promptConfig);

        return <<<HTML
var checkStatus = layui.tableplus.checkStatus(obj.config.id);
if(checkStatus.data.length == 0){
    layui.admin.modal.error("您尚未选择任何条目");
    return;
}
checkStatus.ids = [];
layui.each(checkStatus.data, function(n,v){
    checkStatus.ids.push(v.id);
});
console.log(checkStatus);
parent.layer.confirm(layui.laytpl('{$title}').render(checkStatus), function(index) {
    parent.layer.close(index);
    parent.layer.prompt({$promptConfig}, function(value, index){
        parent.layer.close(index);
        var url = layui.laytpl('{$url}').render(checkStatus);
        console.log(url);
        layui.admin.http.{$method}(url, $.extend({password: value, data: JSON.stringify(checkStatus.data)}, {$sendData}), function(code, msg, data, all){
            layui.admin.reloadTable();
            {$afterDelete}
        });
    });
});
HTML
            ;
    }

    /**
     * @title      _multiverfiy
     * @description
     * @createtime 2019/11/15 6:24 下午
     * @param        $title
     * @param        $url
     * @param array  $sendData
     * @param string $method
     * @param string $afterDelete
     * @return string
     * @author     yangyuance
     */
    public function _verfiy($title, $url, array $sendData = [], $method = "delete", $beforeDelete = '', $afterDelete = '', $promptConfig = [])
    {
        $sendData = json_encode($sendData);

        $beforeDelete = str_replace(["\n", "\r", "'"], ["","", "\'"], $beforeDelete);

        if(empty($promptConfig)) $promptConfig = ['formType' => 1, 'title' => "敏感操作，请验证口令"];

        $promptConfig = json_encode($promptConfig);

        return <<<HTML
parent.layer.confirm(layui.laytpl('{$title}').render(obj.data || {}), function(index) {
    parent.layer.close(index);
    parent.layer.prompt({$promptConfig}, function(value, index){
        parent.layer.close(index);
        var beforeDelete = '{$beforeDelete}', sendData = {};
        if(beforeDelete){
            beforeDelete = (new Function('return function(){' + beforeDelete + '}'))();
        }
        if($.isFunction(beforeDelete)){
            sendData = beforeDelete() || {};
        }
        var url = layui.laytpl('{$url}').render(obj.data || {});
        layui.admin.http.{$method}(url, $.extend({password: value}, sendData, {$sendData}), function(code, msg, data, all){
            layui.admin.reloadTable();
            {$afterDelete}
        });
    });
});
HTML
            ;
    }

    /**
     * @title      getCheckedIds
     * @description
     * @createtime 2019/12/2 4:46 下午
     * @return string
     * @author     yangyuance
     */
    public function getCheckedIds()
    {
        return "var checkStatus = layui.tableplus.checkStatus(obj.config.id);
if(checkStatus.data.length == 0){
    layui.admin.modal.error(\"您尚未选择任何条目\");
    return;
}
var ids = [];
for(var i in checkStatus.data){
    ids.push(checkStatus.data[i].id);
}
obj.data = {id: ids.join(\",\")};";
    }
}