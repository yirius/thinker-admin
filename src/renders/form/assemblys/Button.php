<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;
use Yirius\Admin\templates\TemplateList;
use Yirius\Admin\ThinkerAdmin;

class Button extends Assembly
{
    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        $this->removeClass("layui-input-block")->addClass("layui-btn");
    }


    public function primary()
    {
        $this->addClass("layui-btn-primary");
        return $this;
    }

    public function normal()
    {
        $this->addClass("layui-btn-normal");
        return $this;
    }

    public function warm()
    {
        $this->addClass("layui-btn-warm");
        return $this;
    }

    public function danger()
    {
        $this->addClass("layui-btn-danger");
        return $this;
    }

    public function disabled()
    {
        $this->addClass("layui-btn-disabled");
        return $this;
    }

    public function lg()
    {
        $this->addClass("layui-btn-lg");
        return $this;
    }

    public function sm()
    {
        $this->addClass("layui-btn-sm");
        return $this;
    }

    public function xs()
    {
        $this->addClass("layui-btn-xs");
        return $this;
    }

    public function radius()
    {
        $this->addClass("layui-btn-radius");
        return $this;
    }

    /**
     * @title      on
     * @description 监听On事件
     * @createtime 2020/5/27 3:02 下午
     * @param $event
     * @param $callback
     * @return $this
     * @author     yangyuance
     */
    public function on($event, $callback)
    {
        ThinkerAdmin::script(TemplateList::form()->OnEventJs()->templates([
            $event, $this->getId(), $callback
        ])->render());

        return $this;
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
        return "<button class=\"".$this->getClassString()."\" id=\"".$this->getId()."\" lay-filter=\"".$this->getId()."\" name=\"".$this->getField()."\" ".$this->getAttrString().">".$this->getText()."</button>";
    }
}