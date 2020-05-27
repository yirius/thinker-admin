<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;

class Checkbox extends Radio
{
    protected $inputType = "checkbox";

    /**
     * @title      primary
     * @description
     * @createtime 2020/5/27 3:16 下午
     * @return $this
     * @author     yangyuance
     */
    public function primary() {
        $this->addAttr("lay-skin", "primary");

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
        if(strpos($this->getId(), "[]") === false){
            $this->setTrimId($this->getId() . "[]");
        }

        return parent::render();
    }
}