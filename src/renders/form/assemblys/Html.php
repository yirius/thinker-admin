<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;

class Html extends Assembly
{
    private $plain = false;

    public function plain() {
        $this->plain = true;
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
        if($this->plain) {
            return $this->getValueString();
        }

        return $this->getLabel() . "\n" .
            "<div class=\"".$this->getClassString()."\">\n" .
            "    ".$this->getValueString()."\n" .
            "</div>";
    }
}