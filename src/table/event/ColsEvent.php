<?php


namespace Yirius\Admin\table\event;


use Yirius\Admin\table\ThinkerTable;
use Yirius\Admin\ThinkerAdmin;

class ColsEvent extends ViewEvent
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
     * @title      edit
     * @description
     * @createtime 2019/11/15 5:54 下午
     * @param null   $view
     * @param string $title
     * @param array  $config
     * @return ColsEvent
     * @author     yangyuance
     */
    public function edit($view = null, $title = '编辑信息', array $config = [])
    {
        $config = array_merge([
            'eventName' => "add",
            'area' => ['80%', '80%'],
            'id' => null,
            'data' => []
        ], $config);

        if(is_null($view)) $view = $this->tableIns->getOperateUrl();

        if(strpos($view, "?") != false){
            $viewUrl = explode("?", $view);
            $view = $viewUrl[0] . "?id={{d.id}}&" . $viewUrl[1];
        }else{
            $view = $view . "?id={{d.id}}";
        }

        if(is_null($config['id'])) $config['id'] = $this->tableIns->getId() . "_editdialog";

        return $this->event("edit", $this->_popup($view, $title, $config));
    }

    /**
     * @title      delete
     * @description
     * @createtime 2019/11/15 6:28 下午
     * @param null  $url
     * @param array $sendData
     * @param null  $afterDelete
     * @return ColsEvent
     * @author     yangyuance
     */
    public function delete($url = null, array $sendData = [], $afterDelete = null)
    {
        if(is_null($url)) $url = $this->tableIns->getRestfulUrl();

        if(strpos($url, "?") != false){
            $viewUrl = explode("?", $url);
            $url = $viewUrl[0] . "/{{d.id}}?" . $viewUrl[1];
        }else{
            $url = $url . "/{{d.id}}";
        }

        if(is_null($afterDelete)) $afterDelete = "layer.msg(res.msg || '已删除')";

        return $this->event("delete", $this->_verfiy(
            "是否确认删除该条数据?",
            $url,
            $sendData,
            "delete",
            null,
            $afterDelete
        ));
    }

    /**
     * @title      expend
     * @description
     * @createtime 2019/11/15 6:30 下午
     * @param $html
     * @return ColsEvent
     * @author     yangyuance
     */
    public function expend($html)
    {
        $html = str_replace(["\r\n", "\r", "\n", "'"], ["", "", "", '"'], <<<HTML
<tr class="table-item"><td colspan="{$this->tableIns->getColumnsCount()}">{$html}</td></tr>'
HTML
        );

        return $this->event("expend", <<<HTML
var _this = $(this), tr = _this.parents('tr'), trIndex = tr.data('index');
if($(this).find("i").hasClass('layui-icon-add-1')){
    $(this).find("i").removeClass('layui-icon-add-1').addClass('layui-icon-fonts-del');
    var tableId = 'tableOut_tableIn_' + trIndex;
    var _html = layui.laytpl('{$html}').render(obj.data || {});
    tr.after(_html);
}else{
    $(this).find("i").addClass('layui-icon-add-1').removeClass('layui-icon-fonts-del');
    tr.next().remove();
}
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
layui.table.on('tool({$this->tableIns->getId()})', function(obj){
{$this->event}
});
HTML
        );

        return "";
    }
}