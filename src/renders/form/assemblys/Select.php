<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

class Select extends Assembly
{
    protected $placeholder = "";

    /**
     * @title      setPlaceholder
     * @description
     * @createtime 2020/5/27 3:22 下午
     * @param $placeholder
     * @return $this
     * @author     yangyuance
     */
    public function setPlaceholder($placeholder = "--请选择--")
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

    public function getPlaceholderString() {
        if(empty($this->placeholder)) {
            return "";
        }
        return "<option value=\"\">" . $this->placeholder . "</option>";
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

        $this->addInputClass("layui-input");
    }

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
            if(empty($textValue['childs'])) {
                $result[] = "<option value=\"" . $textValue['value'] . "\" "
                    . ($this->parseIsChecked($textValue) ? "selected=\"selected\"" : "")
                    ." ".(!empty($textValue['disabled']) ? "disabled=\"disabled\"" : "")
                    ." ".(!empty($textValue['readonly']) ? "readonly=\"readonly\"" : "")
                    .">" . $textValue['text'] . "</option>";
            } else {
                $result[] = "<optgroup label=\"" . $textValue['text'] . "\">";
                foreach ($textValue['childs'] as $j => $child) {
                    $result[] = "<option value=\"" . $child['value'] . "\" "
                        . ($this->parseIsChecked($child) ? "selected=\"selected\"" : "")
                        ." ".(!empty($child['disabled']) ? "disabled=\"disabled\"" : "")
                        ." ".(!empty($child['readonly']) ? "readonly=\"readonly\"" : "")
                        .">" . $child['text'] . "</option>";
                }
                $result[] = "</optgroup>";
            }
        }

        return join("", $result);
    }

    /**
     * @title on
     * @description
     * @createtime 2019/3/3 下午9:44
     * @param callback
     * @return $this
     */
    public function on($callback)
    {
        ThinkerAdmin::script("layui.form.on(\"select(".$this->getId().")\", function(obj){\n" .
                $callback . "\n" .
                "});");

        return $this;
    }

    /**
     * @title      search
     * @description
     * @createtime 2020/5/27 3:46 下午
     * @param bool $isSearch
     * @return $this
     * @author     yangyuance
     */
    public function search($isSearch = true)
    {
        if($isSearch){
            $this->addAttr("lay-search", true);
        }else{
            $this->removeAttr("lay-search");
        }

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
            "    <select class=\"". $this->getInputClassString()."\" name=\"".$this->getField()."\" id=\"".$this->getId()."\" lay-filter=\"".$this->getId()."\" ".$this->getAttrString()." >\n" .
            "        ".$this->getPlaceholderString()."\n" .
            "        ".$this->getOptions()."\n" .
            "    </select>\n" .
            "</div>";
    }
}