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

        $area = json_encode($config['area']);
        $data = json_encode($config['data']);

        return <<<HTML
layui.view.popup({
    title: '{$title}',
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
    public function _multiverfiy($title, $url, array $sendData = [], $method = "delete", $afterDelete = '')
    {
        $sendData = json_encode($sendData);

        return <<<HTML
var checkStatus = layui.tableplus.checkStatus(obj.config.id);
if(checkStatus.data.length == 0){
    layui.admin.modal.error("您尚未选择任何条目");
    return;
}
parent.layer.prompt({formType: 1,title: '敏感操作，请验证口令'}, function(value, index){
    parent.layer.close(index);
    parent.layer.confirm('{$title}', function(index) {
        parent.layer.close(index);
        layui.admin.http.{$method}('{$url}', $.extend({password: value, data: JSON.stringify(checkStatus.data)}, {$sendData}), function(code, msg, data, all){
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
    public function _verfiy($title, $url, array $sendData = [], $method = "delete", $beforeDelete = '', $afterDelete = '')
    {
        $sendData = json_encode($sendData);

        $beforeDelete = str_replace(["\n", "\r", "'"], ["","", "\'"], $beforeDelete);

        return <<<HTML
parent.layer.prompt({formType: 1,title: '敏感操作，请验证口令'}, function(value, index){
    parent.layer.close(index);
    parent.layer.confirm('{$title}', function(index) {
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
}