<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/1
 * Time: 下午5:54
 */

namespace Yirius\Admin\table\events;


use Yirius\Admin\Admin;
use Yirius\Admin\Layout;
use Yirius\Admin\table\Table;

class On extends Layout
{
    /**
     * @var Table
     */
    protected $table;

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
     * @createtime 2019/3/1 下午5:59
     * @param $eventName
     * @param $callback
     * @return $this
     */
    public function event($eventName, $callback)
    {
        Admin::script(<<<HTML
layui.table.on('{$eventName}({$this->table->getName()})', function(obj){
{$callback}
});
HTML
        );

        return $this;
    }

    /**
     * @title checkbox
     * @description
     * @createtime 2019/3/1 下午5:59
     * @param null $callback
     * @return On
     */
    public function checkbox($callback)
    {
        return $this->event("checkbox", $callback);
    }

    /**
     * @title row
     * @description
     * @createtime 2019/3/1 下午6:06
     * @param $callback
     * @return On
     */
    public function row($callback)
    {
        return $this->event("row", $callback);
    }

    /**
     * @title rowDouble
     * @description
     * @createtime 2019/3/1 下午6:06
     * @param $callback
     * @return On
     */
    public function rowDouble($callback)
    {
        return $this->event("rowDouble", $callback);
    }

    /**
     * @title sort
     * @description
     * @createtime 2019/3/1 下午6:12
     * @param $callback
     * @return On
     */
    public function sort($callback)
    {
        return $this->event("sort", $callback);
    }

    /**
     * @title edit
     * @description
     * @createtime 2019/3/1 下午6:04
     * @param null $url
     * @param null $callback
     * @return On
     */
    public function edit($url = null, $callback = null)
    {
        if(is_null($url)){
            if(strpos($this->table->getRestfulUrl(), "?") != false){
                $restfulUrl = explode("?", $this->table->getRestfulUrl());
                $url = $restfulUrl[0] . "/{{d.id}}?__type=field&" . $restfulUrl[1];
            }else{
                $url = $this->table->getRestfulUrl() . "/{{d.id}}";
            }
        }

        if(is_null($callback)) $callback = <<<HTML
layui.http.put(layui.laytpl("{$url}").render(obj.data), {value: obj.value, field: obj.field});
HTML
        ;
        return $this->event("edit", $callback);
    }

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        // TODO: Implement render() method.
    }
}