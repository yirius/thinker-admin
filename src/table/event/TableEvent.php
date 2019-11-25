<?php


namespace Yirius\Admin\table\event;


use Yirius\Admin\table\ThinkerTable;
use Yirius\Admin\ThinkerAdmin;

class TableEvent extends ViewEvent
{
    /**
     * @var ThinkerTable
     */
    protected $tableIns;

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
     * @createtime 2019/3/1 下午5:59
     * @param $eventName
     * @param $callback
     * @return $this
     */
    public function event($eventName, $callback)
    {
        ThinkerAdmin::script(<<<HTML
layui.tableplus.on('{$eventName}({$this->tableIns->getId()})', function(obj){
{$callback}
});
HTML
        );
        return $this;
    }

    /**
     * @title      checkbox
     * @description
     * @createtime 2019/11/15 6:43 下午
     * @param $callback
     * @return TableEvent
     * @author     yangyuance
     */
    public function checkbox($callback)
    {
        return $this->event("checkbox", $callback);
    }

    /**
     * @title      row
     * @description
     * @createtime 2019/11/15 6:43 下午
     * @param $callback
     * @return TableEvent
     * @author     yangyuance
     */
    public function row($callback)
    {
        return $this->event("row", $callback);
    }

    /**
     * @title      rowDouble
     * @description
     * @createtime 2019/11/15 6:43 下午
     * @param $callback
     * @return TableEvent
     * @author     yangyuance
     */
    public function rowDouble($callback)
    {
        return $this->event("rowDouble", $callback);
    }

    /**
     * @title      sort
     * @description
     * @createtime 2019/11/15 6:43 下午
     * @param $callback
     * @return TableEvent
     * @author     yangyuance
     */
    public function sort($callback)
    {
        return $this->event("sort", $callback);
    }

    /**
     * @title      edit
     * @description
     * @createtime 2019/11/15 6:43 下午
     * @param null $url
     * @param null $callback
     * @return TableEvent
     * @author     yangyuance
     */
    public function edit($url = null, $callback = null)
    {
        if(is_null($url)) $url = $this->tableIns->getRestfulUrl();

        if(strpos($url, "?") != false){
            $viewUrl = explode("?", $url);
            $url = $viewUrl[0] . "/{{d.id}}?__type=field&" . $viewUrl[1];
        }else{
            $url = $url . "/{{d.id}}?__type=field";
        }

        if(is_null($callback)) $callback = <<<HTML
layui.admin.http.put(layui.laytpl("{$url}").render(obj.data || {}), {value: obj.value, field: obj.field});
HTML
        ;

        return $this->event("edit", $callback);
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
        return "";
    }
}