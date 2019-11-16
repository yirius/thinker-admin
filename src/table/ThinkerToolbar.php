<?php


namespace Yirius\Admin\table;


use Yirius\Admin\extend\ThinkerLayout;
use Yirius\Admin\form\assemblys\Button;
use Yirius\Admin\table\event\ToolbarEvent;
use Yirius\Admin\ThinkerAdmin;

class ThinkerToolbar extends ThinkerLayout
{
    /**
     * @var ThinkerTable
     */
    protected $tableIns;

    /**
     * 当前使用的tool的html
     * @var string
     */
    protected $toolHtml = '';

    /**
     * @var null
     */
    protected $toolbarEvent = null;

    /**
     * ThinkerToolbar constructor.
     * @param ThinkerTable  $tableIns
     * @param callable|null $callback
     */
    public function __construct(ThinkerTable $tableIns, callable $callback = null)
    {
        parent::__construct();

        $this->tableIns = $tableIns;

        if($callback instanceof \Closure){
            call($callback, [$this]);
        }
    }

    /**
     * @title      toolbar
     * @description 设置cols的toolbar
     * @createtime 2019/11/14 11:56 下午
     * @param $html
     * @return $this
     * @author     yangyuance
     */
    public function toolbar($html)
    {
        $this->toolHtml .= $html;

        return $this;
    }

    /**
     * @title      button
     * @description
     * @createtime 2019/11/14 6:40 下午
     * @param       $text
     * @param       $event
     * @param       $icon
     * @param       $class
     * @param bool  $isHref
     * @param array $attrs
     * @return $this
     * @author     yangyuance
     */
    public function button($text, $event, $icon, $class, $isHref = false, $attrs = [])
    {
        return $this->toolbar(
            (new Button())->sm()
                ->setText('<i class="layui-icon layui-icon-'. $icon .'"></i>'.$text)
                ->setClass($class)
                ->setAttrs(($isHref ? 'thinker-href' : 'lay-event') . '="'.$event.'"')
                ->setAttrs($attrs)
                ->render()
        );
    }

    /**
     * @title      add
     * @description
     * @createtime 2019/11/15 3:45 下午
     * @param string $text
     * @param string $icon
     * @return ThinkerToolbar
     * @author     yangyuance
     */
    public function add($text = "添加", $icon = "add-1")
    {
        return $this->button($text, 'add', $icon, '');
    }

    /**
     * @title      delete
     * @description
     * @createtime 2019/11/15 3:45 下午
     * @param string $text
     * @param string $icon
     * @return ThinkerToolbar
     * @author     yangyuance
     */
    public function delete($text = "删除", $icon = "delete")
    {
        return $this->button('删除', 'delete', 'delete', 'layui-btn-danger');
    }

    /**
     * @title      xlsx
     * @description
     * @createtime 2019/11/15 3:47 下午
     * @param string $inText
     * @param string $inIcon
     * @param string $subText
     * @param string $subIcon
     * @return ThinkerToolbar
     * @author     yangyuance
     */
    public function xlsx($inText = "导入EXCEL", $inIcon = "list", $subText = "提交EXCEL", $subIcon = "set")
    {
        $this
            ->button($inText, 'importexcel', $inIcon, 'layui-btn-warm')
            ->button($subText, 'submitexcel', $subIcon, '');

        return $this->toolbar("<input type='file' style='display: none' id='{$this->tableIns->getId()}_xlsximport'/>");
    }


    /**
     * @title      event
     * @description
     * @createtime 2019/11/15 4:56 下午
     * @param callable|null $callable
     * @return ToolbarEvent|null
     * @author     yangyuance
     */
    public function event(callable $callable = null)
    {
        if(is_null($this->toolbarEvent)){
            $this->toolbarEvent = (new ToolbarEvent($this->tableIns, $callable));
        }

        return $this->toolbarEvent;
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
<script type="text/html" id="{$this->tableIns->getId()}_toolbar">
    <div class="layui-btn-container">
        {$this->toolHtml}
    </div>
</script>
HTML
        , false, false, true);

        //渲染一下事件
        if(!is_null($this->toolbarEvent)){
            $this->toolbarEvent->render();
        }

        return "";
    }
}