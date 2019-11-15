<?php


namespace Yirius\Admin\layout;


use Yirius\Admin\extend\ThinkerLayout;

class ThinkerRows extends ThinkerLayout
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
     * @title      columns
     * @description
     * @createtime 2019/11/15 2:19 下午
     * @param callable|null $callable
     * @return $this
     * @author     yangyuance
     */
    public function cols(callable $callable = null)
    {
        $this->layouts[] = (new ThinkerCols($callable));

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
        $layouts = join("\n", array_map(function(ThinkerLayout $value){
            return $value->render();
        }, $this->layouts));

        return <<<HTML
<div class="layui-row {$this->getClass()}" {$this->getAttrs()}>
{$layouts}
</div>
HTML;
    }
}