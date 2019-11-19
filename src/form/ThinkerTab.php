<?php


namespace Yirius\Admin\form;


use Yirius\Admin\extend\ThinkerLayout;
use Yirius\Admin\layout\ThinkerPage;
use Yirius\Admin\ThinkerAdmin;

class ThinkerTab extends ThinkerLayout
{
    use setExtend;

    protected $title = "tab";

    /**
     * @title      setTitle
     * @description
     * @createtime 2019/11/16 11:35 下午
     * @param $title
     * @return $this
     * @author     yangyuance
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @title getTitle
     * @description
     * @createtime 2019/3/12 下午11:03
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @title      inline
     * @description
     * @createtime 2019/11/16 11:35 下午
     * @param callable $callable
     * @return ThinkerInline
     * @author     yangyuance
     */
    public function inline(callable $callable = null)
    {
        $inline = (new ThinkerInline($callable, $this->getValue()));

        $this->assemblys[] = $inline;

        return $inline;
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
        return join("\n", array_map(function(ThinkerLayout $value){
            return '<div class="layui-form-item">' . ($value->render()) . "</div>";
        }, $this->assemblys));
    }
}