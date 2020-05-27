<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

class ColorPicker extends Assembly
{
    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        ThinkerAdmin::script("colorpicker", false, true);
    }

    /**
     * @title format
     * @description
     * @createtime 2019/3/9 下午11:50
     * @return $this
     * @throws \Exception
     */
    public function format($type = "rgb")
    {
        $this->addAttr("data-format", type);

        return $this;
    }

    /**
     * @title alpha
     * @description
     * @createtime 2019/3/9 下午11:50
     * @return $this
     * @throws \Exception
     */
    public function alpha()
    {
        $this->addAttr("data-alpha", true);

        return $this;
    }

    /**
     * @title colors
     * @description
     * @createtime 2019/3/9 下午11:51
     * @return $this
     * @throws \Exception
     */
    public function colors(array $colors)
    {
        $this->addAttr("data-predefine", true);

        $this->addAttr("data-colors", json_encode(colors));

        return $this;
    }

    /**
     * @title size
     * @description
     * @createtime 2019/3/9 下午11:52
     * @return $this
     * @throws \Exception
     */
    public function size($size = "lg")
    {
        $this->addAttr("data-size", $size);

        return $this;
    }

    /**
     * @title onChange
     * @description
     * @createtime 2019/3/10 上午12:04
     * @param $callback
     * @return $this
     * @throws \Exception
     */
    public function onChange($callback)
    {
        $this->addAttr("data-change", $callback);

        return $this;
    }

    /**
     * @title onChange
     * @description
     * @createtime 2019/3/10 上午12:04
     * @param $callback
     * @return $this
     * @throws \Exception
     */
    public function onDone($callback)
    {
        $this->addAttr("data-done", $callback);

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
        return $this->getLabel() . "\n" .
            "<div class=\"".$this->getClassString()."\">\n" .
            "    <div id=\"".$this->getId()."\" lay-filter=\"".$this->getId()."\" data-color=\"".$this->getValue()."\" ".$this->getAttrString()." lay-colorpicker=\"\"></div>\n" .
            "    <input type=\"hidden\" id=\"".$this->getId()."_hidden\" name=\"".$this->getField()."\" value=\"".$this->getValue()."\"/>\n" .
            "</div>";
    }
}