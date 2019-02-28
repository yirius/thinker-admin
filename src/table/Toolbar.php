<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/27
 * Time: 上午12:35
 */

namespace Yirius\Admin\table;


use Yirius\Admin\Admin;
use Yirius\Admin\Layout;
use \Yirius\Admin\table\events\Toolbar as ToolbarEvent;

class Toolbar extends Layout
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * @var ToolbarEvent
     */
    protected $event = null;

    /**
     * @var string
     */
    protected $toolbar = '';

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
     * @title tool
     * @description
     * @createtime 2019/2/26 下午5:36
     * @param $html
     * @return $this
     */
    public function toolbar($html)
    {
        $this->toolbar .= $html;

        return $this;
    }

    /**
     * @title button
     * @description
     * @createtime 2019/2/27 上午1:11
     * @param $text
     * @param $event
     * @param $icon
     * @param $class
     * @return Toolbar
     */
    public function button($text, $event, $icon, $class)
    {
        return $this->toolbar('<a class="layui-btn layui-btn-sm '. $class .'" lay-event="'. $event .'"><i class="layui-icon layui-icon-'. $icon .'"></i>'. $text .'</a>');
    }

    /**
     * @title edit
     * @description
     * @createtime 2019/2/27 上午1:11
     * @return Toolbar
     */
    public function add()
    {
        return $this->button('编辑', 'add', 'add-1', '');
    }

    /**
     * @title delete
     * @description
     * @createtime 2019/2/27 上午1:11
     * @return Toolbar
     */
    public function delete()
    {
        return $this->button('删除', 'delete', 'delete', 'layui-btn-danger');
    }

    /**
     * @title event
     * @description
     * @createtime 2019/2/27 下午12:05
     * @param \Closure|null $callback
     * @return ToolbarEvent
     */
    public function event(\Closure $callback = null)
    {
        $this->event = (new ToolbarEvent($this->table, $callback));

        return $this->event;
    }

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        if(!is_null($this->event)){
            Admin::script($this->event->render());
        }

        return <<<HTML
<script type="text/html" id="{$this->table->getName()}_toolbar">
    <div class="layui-btn-container">
        {$this->toolbar}
    </div>
</script>
HTML;
    }
}