<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;
use Yirius\Admin\templates\TemplateList;
use Yirius\Admin\ThinkerAdmin;

class Hidden extends Assembly
{
    /**
     * @title      on
     * @description
     * @createtime 2020/5/27 3:26 下午
     * @param $event
     * @param $callback
     * @return mixed
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
        return "<input type=\"hidden\" name=\"".$this->getField()."\" id=\"".$this->getId()."\" lay-filter=\"".$this->getId()."\" value=\"".$this->getValueString()."\" ".$this->getAttrString()." />";
    }
}