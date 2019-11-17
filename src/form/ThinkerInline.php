<?php


namespace Yirius\Admin\form;


use Yirius\Admin\extend\ThinkerLayout;
use Yirius\Admin\layout\ThinkerPage;
use Yirius\Admin\ThinkerAdmin;

class ThinkerInline extends ThinkerLayout
{
    use setExtend;

    /**
     * @title       render
     * @description 每一个组件需要继承渲染接口
     * @createtime  2019/11/14 4:26 下午
     * @return string
     * @author      yangyuance
     */
    public function render()
    {
        return join("\n", array_map(function(ThinkerLayout $value){
            return '<div class="layui-inline">' . ($value
                    ->removeClass('layui-input-block')
                    ->setClass('layui-input-inline')
                    ->render()) . "</div>";
        }, $this->assemblys));
    }
}