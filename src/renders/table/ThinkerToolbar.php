<?php


namespace Yirius\Admin\renders\table;


use Yirius\Admin\renders\form\assemblys\Button;
use Yirius\Admin\renders\table\event\ToolbarEvent;
use Yirius\Admin\renders\ThinkerTable;
use Yirius\Admin\support\abstracts\LayoutAbstract;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class ThinkerToolbar
 * @package Yirius\Admin\renders\table
 *
 */
class ThinkerToolbar extends LayoutAbstract
{
    protected $thinkerTable = null;

    protected $toolbarEvent = null;

    protected $toolHtml = "";

    public function __construct(ThinkerTable $thinkerTable, callable $callback = null)
    {
        parent::__construct();

        $this->thinkerTable = $thinkerTable;

        if(is_callable($callback)) call($callback, [$this]);
    }

    public function toolbar($html)
    {
        $this->toolHtml .= $html;

        return $this;
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 6:03 下午
     * @param       $text
     * @param       $event
     * @param       $icon
     * @param       $useClass
     * @param bool  $isHref
     * @param array $attrs
     * @param null  $ifTpl
     * @return ThinkerToolbar
     * @author     yangyuance
     */
    public function button($text, $event, $icon, $useClass, $isHref = false, $attrs = [], $ifTpl = null)
    {
        $button = (new Button("", '<i class="layui-icon layui-icon-'. $icon .'"></i>'.$text));

        $button->setTrimId($this->getId() . "_toolbar_" . $event)
            ->addClass($useClass)->addAttr($isHref ? "lay-href" : "lay-event", $event);

        if(!empty($attrs)) {
            foreach ($attrs as $i => $attr) {
                $button->addAttr($i, $attr);
            }
        }

        return $this->toolbar(
            empty($ifTpl) ? $button->xs()->render() : '{{# '.$ifTpl.'{ }}'.$button->xs()->render().'{{# } }}'
        );
    }

    public function add($text = "添加", $icon = "add-1")
    {
        return $this->button($text, 'add', $icon, '');
    }

    public function delete($text = "删除", $icon = "delete")
    {
        return $this->button('删除', 'delete', 'delete', 'layui-btn-danger');
    }

    public function xlsx($inText = "导入EXCEL", $inIcon = "list", $subText = "提交EXCEL", $subIcon = "set")
    {
        $this
            ->button($inText, 'importexcel', $inIcon, 'layui-btn-warm')
            ->button($subText, 'submitexcel', $subIcon, '');

        return $this->toolbar("<input type='file' style='display: none' id='{$this->thinkerTable->getId()}_xlsximport'/>");
    }

    /**
     * @title      event
     * @description
     * @createtime 2020/5/27 6:04 下午
     * @param callable|null $callable
     * @return ToolbarEvent|null
     * @author     yangyuance
     */
    public function event(callable $callable = null)
    {
        if(is_null($this->toolbarEvent)){
            $this->toolbarEvent = (new ToolbarEvent($this->thinkerTable, $callable));
        }

        return $this->toolbarEvent;
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
<script type="text/html" id="{$this->thinkerTable->getId()}_toolbar">
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