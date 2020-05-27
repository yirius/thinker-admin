<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

class Radio extends Assembly
{
    protected $inputType = "radio";

    private $textValues = [];

    /**
     * @title      setTextValues
     * @description
     * @createtime 2020/5/27 3:06 下午
     * @param $textValues
     * @return $this
     * @author     yangyuance
     */
    public function setTextValues($textValues)
    {
        $this->textValues = $textValues;

        return $this;
    }

    /**
     * @return array
     */
    public function getTextValues()
    {
        return $this->textValues;
    }

    /**
     * @title      on
     * @description
     * @createtime 2020/5/27 3:07 下午
     * @param $callback
     * @return $this
     * @author     yangyuance
     */
    public function on($callback) {
        ThinkerAdmin::script("layui.form.on(\"".$this->inputType."(".$this->getId().")\", function(obj){\n" .
                $callback . "\n" .
                "});");

        return $this;
    }

    /**
     * @title      options
     * @description 设置选项参数
     * @createtime 2020/5/27 3:08 下午
     * @param array $textValues
     * @return $this
     * @author     yangyuance
     */
    public function options(array $textValues) {
        $this->textValues = $textValues;
        return $this;
    }

    /**
     * @title      getOptions
     * @description 获取参数
     * @createtime 2020/5/27 3:15 下午
     * @return string
     * @author     yangyuance
     */
    public function getOptions() {
        $result = [];
        foreach ($this->textValues as $i => $textValue) {
            $result[] = "<input type=\"".$this->inputType
                ."\" value=\"".$textValue['value']
                ."\" title=\"".$textValue['text']
                ."\" name=\"".$this->getField()
                ."\" id=\"".$this->getId().$i
                ."\" lay-filter=\"".$this->getId()
                ."\" ".$this->getAttrString()
                ." ".($this->parseIsChecked($textValue) ? "checked=\"checked\"" : "")
                ." ".(!empty($textValue['disabled']) ? "disabled=\"disabled\"" : "")
                ." ".(!empty($textValue['readonly']) ? "readonly=\"readonly\"" : "")
                ." />";
        }

        return join("", $result);
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
            "    ".$this->getOptions()."\n" .
            "</div>";
    }
}