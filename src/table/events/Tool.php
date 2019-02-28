<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/26
 * Time: 下午10:54
 */

namespace Yirius\Admin\table\events;


use Yirius\Admin\Layout;
use Yirius\Admin\table\Table;

class Tool extends Layout
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * @var string
     */
    protected $event = '';

    /**
     * ToolEvent constructor.
     * @param $table
     * @param \Closure|null $callback
     */
    public function __construct($table, \Closure $callback = null)
    {
        $this->table = $table;

        if($callback instanceof \Closure){
            call($callback, [$this]);
        }
    }

    /**
     * @title event
     * @description
     * @createtime 2019/2/26 下午10:56
     * @param $eventName
     * @param $callback
     * @return $this
     */
    public function event($eventName, $callback)
    {
        $this->event .= <<<HTML
if(obj.event === "{$eventName}"){
    {$callback}
}
HTML;

        return $this;
    }

    /**
     * @title edit
     * @description
     * @createtime 2019/2/26 下午10:57
     * @param $view
     * @param string $title
     * @param array $area
     * @param null $id
     * @param array $data
     * @return Tool
     */
    public function edit($view = null, $title = '编辑信息',array $area = ['80%', '80%'], $id = null, array $data = [])
    {
        if(is_null($view)) $view = $this->table->getEditPath() . "?id={{d.id}}";

        if(is_null($id)) $id = $this->table->getName() . "_dialog";

        $area = json_encode($area);

        $data = json_encode($data);

        return $this->event("edit", <<<HTML
layui.view.dialog({
    title: '{$title}',
    area: {$area},
    id: '{$id}',
    success: function(layero, index){
        layui.view.init("#" + this.id).render(
            layui.tools.getCorrectUrl('{$view}', obj.data), {$data}
        ).done(function(){});
    }
});
HTML
        );
    }

    /**
     * @title delete
     * @description
     * @createtime 2019/2/26 下午11:46
     * @param $url
     * @param null $tableName
     * @param array $sendData
     * @param null $afterDelete
     * @return Tool
     */
    public function delete($url = null, $tableName = null, array $sendData = [], $afterDelete = null)
    {
        if(is_null($url)) $url = $this->table->getRestfulUrl() . "/{{d.id}}";

        if(is_null($afterDelete))
        {
            $afterDelete = '';

            if(!is_null($tableName)) $afterDelete .= 'table.reload("'. $tableName .'")';

            $afterDelete .= "layer.msg(res.msg || '已删除')";
        }

        $sendData = json_encode($sendData);

        return $this->event("delete", <<<HTML
layer.prompt({formType: 1,title: '敏感操作，请验证口令'}, function(value, index){
    layer.close(index);
    layer.confirm('确定删除吗？', function(index) {
        var url = layui.laytpl('{$url}').render(obj.data);
        layui.http.delete(url, $.extend({password: value}, {$sendData}), function(res){
            {$afterDelete}
        });
    });
});
HTML
        );
    }

    /**
     * @title expend
     * @description
     * @createtime 2019/2/27 下午4:40
     * @param $html
     * @return Tool
     */
    public function expend($html)
    {
        return $this->event("expend", <<<HTML
var _this = $(this), tr = _this.parents('tr'), trIndex = tr.data('index');
if($(this).find("i").hasClass('layui-icon-add-1')){
    $(this).find("i").removeClass('layui-icon-add-1').addClass('layui-icon-fonts-del');
    var tableId = 'tableOut_tableIn_' + trIndex;
    var _html = layui.laytpl('<tr class="table-item">{$html}</tr>').render(obj.data);
    tr.after(_html);
}else{
    $(this).find("i").addClass('layui-icon-add-1').removeClass('layui-icon-fonts-del');
    tr.next().remove();
}
HTML
        );
    }

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        return <<<HTML
//tool's event
layui.table.on('tool({$this->table->getName()})', function(obj){
{$this->event}
});
HTML;
    }
}