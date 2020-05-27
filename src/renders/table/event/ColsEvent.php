<?php


namespace Yirius\Admin\renders\table\event;


use Yirius\Admin\renders\ThinkerTable;
use Yirius\Admin\templates\TemplateList;
use Yirius\Admin\ThinkerAdmin;

class ColsEvent extends ViewEvent
{
    /**
     * @var ThinkerTable
     */
    protected $thinkerTable = null;

    /**
     * 事件
     * @var string
     */
    protected $event = "";

    /**
     * ColsEvent constructor.
     * @param ThinkerTable  $thinkerTable
     * @param \Closure|null $closure
     */
    public function __construct(ThinkerTable $thinkerTable, \Closure $closure = null)
    {
        parent::__construct();

        $this->thinkerTable = $thinkerTable;

        if(is_callable($closure)) call($closure, [$this]);
    }

    /**
     * @title      event
     * @description
     * @createtime 2020/5/27 6:14 下午
     * @param $eventName
     * @param $callback
     * @return $this
     * @author     yangyuance
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
     * @createtime 2020/5/27 6:15 下午
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

        if(is_null($view)) $view = $this->thinkerTable->getOperateUrl();

        if(strpos($view, "?") != false){
            $viewUrl = explode("?", $view);
            $view = $viewUrl[0] . "?id={{d.id}}&" . $viewUrl[1];
        }else{
            $view = $view . "?id={{d.id}}";
        }

        if(is_null($config['id'])) $config['id'] = $this->thinkerTable->getId() . "_editdialog";

        return $this->event("edit", $this->_popup($view, $title, $config));
    }

    /**
     * @title      delete
     * @description
     * @createtime 2020/5/27 6:16 下午
     * @param null  $url
     * @param array $sendData
     * @param null  $afterDelete
     * @param null  $confirmPop
     * @return ColsEvent
     * @author     yangyuance
     */
    public function delete($url = null, array $sendData = [], $afterDelete = null, $confirmPop = null)
    {
        if(is_null($url)) $url = $this->thinkerTable->getRestfulUrl();

        if(strpos($url, "?") != false){
            $viewUrl = explode("?", $url);
            $url = $viewUrl[0] . "/{{d.id}}?" . $viewUrl[1];
        }else{
            $url = $url . "/{{d.id}}";
        }

        if(is_null($afterDelete)) $afterDelete = "layer.msg(res.msg || '已删除')";
        else $afterDelete = "layer.msg(msg || \"已删除\");\n" . $afterDelete;

        if(empty($confirmPop)) $confirmPop = "是否确认删除该条数据?";

        return $this->event("delete", $this->_verify(
            $confirmPop,
            $url,
            $sendData,
            "delete",
            null,
            $afterDelete,
            null
        ));
    }

    /**
     * @title      expend
     * @description
     * @createtime 2020/5/27 6:18 下午
     * @param $html
     * @return ColsEvent
     * @author     yangyuance
     */
    public function expend($html)
    {
        $html = str_replace(["\r\n", "\r", "\n", "'"], ["", "", "", '"'], <<<HTML
<tr class="table-item"><td colspan="{$this->thinkerTable->getColumnsCount()}">{$html}</td></tr>'
HTML
        );

        return $this->event("expend", TemplateList::table()->event()->ColsEventExpendJs()->templates([
            $html
        ])->render());
    }

    /**
     * @title       render
     * @description 每一个组件需要继承渲染接口
     * @createtime  2020/5/27 1:58 下午
     * @return string
     * @author      yangyuance
     */
    public function render()
    {
        ThinkerAdmin::script(<<<HTML
layui.tableplus.on('tool({$this->thinkerTable->getId()})', function(obj){
{$this->event}
});
HTML
        );

        return "";
    }

}