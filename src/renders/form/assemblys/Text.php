<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;
use Yirius\Admin\templates\TemplateList;
use Yirius\Admin\ThinkerAdmin;

class Text extends Assembly
{
    protected $inputType = "text";

    /**
     * @title      setInputType
     * @description
     * @createtime 2020/5/27 3:21 下午
     * @param $inputType
     * @return $this
     * @author     yangyuance
     */
    public function setInputType($inputType)
    {
        $this->inputType = $inputType;

        return $this;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return $this->inputType;
    }

    protected $placeholder = "";

    /**
     * @title      setPlaceholder
     * @description
     * @createtime 2020/5/27 3:22 下午
     * @param $placeholder
     * @return $this
     * @author     yangyuance
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @var array
     */
    protected $inputClass = [];

    /**
     * @title      setInputClass
     * @description
     * @createtime 2020/5/27 3:25 下午
     * @param $inputClass
     * @return $this
     * @author     yangyuance
     */
    public function setInputClass($inputClass)
    {
        $this->inputClass = $inputClass;
        return $this;
    }

    public function addInputClass($inputClass)
    {
        $this->inputClass[] = $inputClass;
        return $this;
    }

    /**
     * @return array
     */
    public function getInputClass()
    {
        return $this->inputClass;
    }

    /**
     * @title      getInputClassString
     * @description
     * @createtime 2020/5/27 3:27 下午
     * @return string
     * @author     yangyuance
     */
    public function getInputClassString()
    {
        return join(" ", $this->inputClass);
    }

    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        $this->inputClass[] = "layui-input";
    }

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
        return $this->getLabel() . "\n" .
            "<div class=\"".$this->getClassString()."\">\n" .
            "    <input type=\"".$this->inputType."\" class=\"".$this->getInputClassString()."\" name=\"".$this->getField()."\" id=\"".$this->getId()."\" lay-filter=\"".$this->getId()."\" value=\"" . $this->getValueString() . "\" placeholder=\"".$this->getPlaceholder()."\" ".$this->getAttrString()." />\n" .
            "</div>";
    }
}