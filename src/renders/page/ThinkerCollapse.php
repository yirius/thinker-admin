<?php


namespace Yirius\Admin\renders\page;

use Yirius\Admin\support\abstracts\LayoutAbstract;

class ThinkerCollapse extends LayoutAbstract
{
    protected $layouts = [];

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
        $this->layouts[] = new ThinkerCollapseItem($callable);
        return $this;
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
        $layouts = join("\n", array_map(function (LayoutAbstract $abstract) {
            return $abstract->render();
        }, $this->layouts));

        return <<<HTML
<div class="layui-collapse {$this->getClassString()}" {$this->getAttrString()} lay-accordion>
    {$layouts}
</div>
HTML;

    }
}