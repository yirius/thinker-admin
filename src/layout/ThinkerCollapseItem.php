<?php


namespace Yirius\Admin\layout;


use Yirius\Admin\extend\ThinkerLayout;

class ThinkerCollapseItem extends ThinkerLayout
{
    protected $title = '';

    protected $content = '';

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
     * @title      setTitle
     * @description
     * @createtime 2019/11/15 3:19 下午
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
     * @title      setContent
     * @description
     * @createtime 2019/11/15 3:19 下午
     * @param $content
     * @return $this
     * @author     yangyuance
     */
    public function setContent($content)
    {
        $this->content = $content;

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
        return <<<HTML
<div class="layui-colla-item {$this->getClass()}" {$this->getAttrs()}>
    <h2 class="layui-colla-title">{$this->title}</h2>
    <div class="layui-colla-content layui-show">{$this->content}</div>
</div>
HTML;

    }
}