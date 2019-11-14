<?php


namespace Yirius\Admin\layout;


use Yirius\Admin\extend\ThinkerLayout;

class ThinkerPage extends ThinkerLayout
{
    /**
     * ThinkerPage constructor.
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
     * @title       render
     * @description 每一个组件需要继承渲染接口
     * @createtime  2019/11/14 4:26 下午
     * @return string
     * @author      yangyuance
     */
    public function render()
    {
        return response(<<<HTML
<title>{$this->title}</title>
{$this->formatStyle()}
{$this->getBreadcrumb()}
<div class="layui-fluid">
    {$this->formatLayouts()}
</div>
{$this->formatScript()}
HTML
        );
    }
}