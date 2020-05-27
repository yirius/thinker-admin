<?php


namespace Yirius\Admin\renders\form;


use Yirius\Admin\support\abstracts\LayoutAbstract;

class ThinkerInline extends ThinkerAssemblys
{
    public function __construct(callable $closure = null, $useValue = [])
    {
        parent::__construct();

        $this->setUseValue($useValue);

        if(is_callable($closure)) call($closure, [$this]);
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
        return join("\n", array_map(function(LayoutAbstract $value){
            return '<div class="layui-inline">' . ($value->removeClass("layui-input-block")->addClass("layui-input-inline")->render()) . "</div>";
        }, $this->assemblys));
    }
}