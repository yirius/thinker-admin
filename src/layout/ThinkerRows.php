<?php


namespace Yirius\Admin\layout;


use Yirius\Admin\extend\ThinkerLayout;

/**
 * Class ThinkerRows
 * @package Yirius\Admin\layout
 */
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
     * @title      cols
     * @description
     * @createtime 2019/11/19 7:15 下午
     * @param callable|null $callable
     * @return ThinkerCols
     * @author     yangyuance
     */
    public function cols(callable $callable = null)
    {
        $cols = (new ThinkerCols($callable));

        $this->layouts[] = $cols;

        return $cols;
    }

    /**
     * @title      space
     * @description
     * @createtime 2019/11/19 8:25 下午
     * @param $value
     * @return $this
     * @author     yangyuance
     */
    public function space($value)
    {
        $this->setClass("layui-col-space".$value);

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
        $layouts = join("\n", $this->layouts);

        return <<<HTML
<div class="layui-row {$this->getClass()}" {$this->getAttrs()}>
{$layouts}
</div>
HTML;
    }
}