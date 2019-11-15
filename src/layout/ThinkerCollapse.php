<?php


namespace Yirius\Admin\layout;


use Yirius\Admin\extend\ThinkerLayout;

class ThinkerCollapse extends ThinkerLayout
{
    use setLayout;

    /**
     * ThinkerRows constructor.
     * @param callable|null $callable
     */
    public function __construct(callable $callable = null)
    {
        parent::__construct();

        if(is_callable($callable)){
            call($callable, [$this]);
        }
    }

    /**
     * @title      addItem
     * @description
     * @createtime 2019/11/15 3:21 下午
     * @param callable|null $callable
     * @return ThinkerCollapse
     * @author     yangyuance
     */
    public function addItem(callable $callable = null)
    {
        return $this->setLayouts((new ThinkerCollapseItem($callable)));
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
        $layouts = join("\n", $this->layouts);

        return <<<HTML
<div class="layui-collapse {$this->getClass()}" {$this->getAttrs()} lay-accordion>
    {$layouts}
</div>
HTML;

    }
}