<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;

class Textarea extends Text
{
    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        $this->inputClass = [];
        $this->inputClass[] = "layui-textarea";
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
        return $this->getLabel() . "\n" .
            "<div class=\"".$this->getClassString()."\">\n" .
            "    <textarea class=\"". $this->getInputClassString()."\" name=\"".$this->getField()."\" id=\"".$this->getId()."\" lay-filter=\"".$this->getId()."\" placeholder=\"".$this->getPlaceholder()."\" ".$this->getAttrString()." >".$this->getValueString()."</textarea>\n" .
            "</div>";
    }
}