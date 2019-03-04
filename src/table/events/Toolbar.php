<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/27
 * Time: 下午12:02
 */

namespace Yirius\Admin\table\events;


use Yirius\Admin\Layout;
use Yirius\Admin\table\Table;

class Toolbar extends Layout
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
     * @title eventAdd
     * @description
     * @createtime 2019/2/27 上午1:20
     * @param $view
     * @param string $title
     * @param array $area
     * @param null $id
     * @param array $data
     * @return Toolbar
     */
    public function add($view = null, $title = '添加信息',array $area = ['80%', '80%'], $id = null, array $data = [])
    {
        if(is_null($view)) $view = $this->table->getEditPath();

        if(is_null($id)) $id = $this->table->getName() . "_adddialog";

        $area = json_encode($area);

        $data = json_encode($data);

        return $this->event("add", <<<HTML
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
     * @title eventDelete
     * @description
     * @createtime 2019/2/27 上午1:20
     * @param $url
     * @param null $tableName
     * @param array $sendData
     * @param null $afterDelete
     * @return Toolbar
     */
    public function delete($url = null, $tableName = null, array $sendData = [], $afterDelete = null)
    {
        if(is_null($url)) $url = $this->table->getRestfulUrl();

        if(is_null($afterDelete))
        {
            $afterDelete = '';

            if(!is_null($tableName)) $afterDelete .= 'table.reload("'. $tableName .'")';

            $afterDelete .= "layer.msg(res.msg || '已删除')";
        }

        $sendData = json_encode($sendData);

        return $this->event("delete", <<<HTML
var checkStatus = layui.table.checkStatus(obj.config.id);
if(checkStatus.data.length == 0){
    layui.layer.alert("您尚未选择任何条目");
    return;
}
layer.prompt({formType: 1,title: '敏感操作，请验证口令'}, function(value, index){
    layer.close(index);
    layer.confirm('确定删除吗？', function(index) {
        layui.http.delete('{$url}', $.extend({password: value, data: checkStatus.data}, {$sendData}), function(res){
            if(layui.table){
                for(var i in layui.table.cache){
                    layui.table.reload(i);
                }
            }
            {$afterDelete}
        });
    });
});
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
//toolbar's event
layui.table.on('toolbar({$this->table->getName()})', function(obj){
{$this->event}
});
HTML;
    }
}