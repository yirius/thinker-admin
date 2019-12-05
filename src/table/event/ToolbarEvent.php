<?php


namespace Yirius\Admin\table\event;

use Yirius\Admin\table\ThinkerTable;
use Yirius\Admin\ThinkerAdmin;

class ToolbarEvent extends ViewEvent
{
    /**
     * @var ThinkerTable
     */
    protected $tableIns;

    /**
     * 事件
     * @var string
     */
    protected $event = "";

    /**
     * ToolbarEvent constructor.
     * @param ThinkerTable  $tableIns
     * @param \Closure|null $callback
     */
    public function __construct(ThinkerTable $tableIns, \Closure $callback = null)
    {
        parent::__construct();

        $this->tableIns = $tableIns;

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
     * @title      add
     * @description
     * @createtime 2019/11/15 4:49 下午
     * @param null   $view
     * @param string $title
     * @param array  $config
     * @return ToolbarEvent
     * @author     yangyuance
     */
    public function add($view = null, $title = '添加信息', array $config = [])
    {
        $config = array_merge([
            'eventName' => "add",
            'area' => ['80%', '80%'],
            'id' => null,
            'data' => []
        ], $config);

        if(is_null($view)) $view = $this->tableIns->getOperateUrl();
        if(is_null($config['id'])) $config['id'] = $this->tableIns->getId() . "_adddialog";

        return $this->event($config['eventName'], $this->_popup($view, $title, $config));
    }

    /**
     * @title      delete
     * @description
     * @createtime 2019/11/15 6:22 下午
     * @param null  $url
     * @param array $sendData
     * @param null  $afterDelete
     * @return ToolbarEvent
     * @author     yangyuance
     */
    public function delete($url = null, array $sendData = [], $afterDelete = null)
    {
        if(is_null($url)) $url = $this->tableIns->getRestfulUrl();
        if(is_null($afterDelete)) $afterDelete = "layer.msg(res.msg || '已删除')";

        return $this->event(
            "delete",
            $this->_multiverfiy("确定删除选中数据?", $url, $sendData, "delete", $afterDelete)
        );
    }

    /**
     * @title      xlsx
     * @description
     * @createtime 2019/11/15 5:44 下午
     * @param        $url
     * @param string $parseData
     * @param array  $sendData
     * @param array  $config
     * @return ToolbarEvent
     * @author     yangyuance
     */
    public function xlsx($url, $parseData = '', array $sendData = [], array $config = [])
    {
        $config = array_merge([
            'afterReload' => '',
            'afterRequest' => 'layui.admin.reloadTable();
                                parent.layui.layer.closeAll();
                                parent.layui.layer.msg(res.msg);',
            'errorRequest' => <<<HTML
var tableBody = $("[lay-id='{$this->tableIns->getId()}']").find("table").eq(1).find("tbody").find("tr");
var hasErrorIndex = [];
for(var i in data){
    hasErrorIndex.push(parseInt(i));
}
tableBody.each(function(n,v){
    if(hasErrorIndex.indexOf(n) >= 0){
        $(v).css("color", "red");
    }else{
        $(v).css("color", "");
    }
});
HTML
            ,
            'method' => 'post'
        ], $config);

        //加入excel的使用
        ThinkerAdmin::script("excel", false, true);

        //转义一下字符
        $parseData = str_replace(["'", "\n", "\r"], ["\'", "", ""], $parseData);

        //监听input的变化
        ThinkerAdmin::script(<<<HTML
$(document).off("change", "#{$this->tableIns->getId()}_xlsximport")
.on("change", "#{$this->tableIns->getId()}_xlsximport", function(e){
    layui.excel.importExcel(this.files, {}, function(data){
        //找到第一个Sheet
        var useSheet = [];
        for(var i in data[0]){
            useSheet = data[0][i];
            break;
        }
        var parseData = '{$parseData}';
        if(parseData) useSheet = (new Function('return '+parseData))()(useSheet);
        layui.tableplus.reload('{$this->tableIns->getId()}', {
            data: useSheet,
            limit: useSheet.length,
            limits: [useSheet.length]
        });
        {$config['afterReload']}
    });
});
HTML
        );

        $sendData = json_encode($sendData);

        $this->event("submitexcel", <<<HTML
var resultData = layui.tableplus.cache[obj.config.id];
if(resultData.length == 0){
    layui.admin.modal.error("您尚未导入excel");
    return;
}
layer.confirm('是否确定导入'+ resultData.length +'条数据？', function(index) {
    parent.layer.close(index);
    layui.admin.http.{$config['method']}('{$url}', $.extend({data: JSON.stringify(resultData)}, {$sendData}), function(code, msg, data, all){
        {$config['afterRequest']}
    }, function(code, msg, data, all){
        {$config['errorRequest']}
    });
});
HTML
        );

        return $this->event("importexcel", <<<HTML
$("#{$this->tableIns->getId()}_xlsximport").click();
HTML
        );
    }

    /**
     * @title       render
     * @description 每一个组件需要继承渲染接口
     * @createtime  2019/11/14 4:26 下午
     * @return string
     * @author      yangyuance
     */
    public function render()
    {
        ThinkerAdmin::script(<<<HTML
layui.tableplus.on('toolbar({$this->tableIns->getId()})', function(obj){
{$this->event}
});
HTML
        );

        return "";
    }
}