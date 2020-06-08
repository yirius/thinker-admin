<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\DataAssembly;
use Yirius\Admin\templates\TemplateList;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class Tree
 * @package Yirius\Admin\renders\form\assemblys
 *
 * @method Tree setShowCheckbox(bool $value);
 * @method Tree setEdit($value);
 * @method Tree setAccordion(bool $value);
 * @method Tree setOnlyIconControl(bool $value);
 * @method Tree setIsJump(bool $value);
 * @method Tree setShowLine(bool $value);
 */
class Tree extends DataAssembly
{
    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        $name = explode("\\", get_class($this));

        ThinkerAdmin::script(strtolower($name[count($name) - 1]), false, true);

        $this->configsFields = array_merge([
            "showCheckbox", "edit", "accordion", "onlyIconControl", "isJump", "showLine"
        ], $this->configsFields);
    }

    //私有变量
    private $clickEvent = "";
    private $checkedEvent = "";
    private $operateEvent = "";
    private $beforeOperateEvent = "";

    /**
     * @title      setClickEvent
     * @description
     * @createtime 2020/5/27 4:23 下午
     * @param $clickEvent
     * @return $this
     * @author     yangyuance
     */
    public function setClickEvent($clickEvent)
    {
        $this->clickEvent = $clickEvent;

        return $this;
    }

    /**
     * @return string
     */
    public function getClickEvent()
    {
        return $this->clickEvent;
    }

    /**
     * @title      setCheckedEvent
     * @description
     * @createtime 2020/5/27 4:23 下午
     * @param $checkedEvent
     * @return $this
     * @author     yangyuance
     */
    public function setCheckedEvent($checkedEvent)
    {
        $this->checkedEvent = $checkedEvent;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckedEvent()
    {
        return $this->checkedEvent;
    }

    /**
     * @title      setOperateEvent
     * @description
     * @createtime 2020/5/27 4:23 下午
     * @param $operateEvent
     * @return $this
     * @author     yangyuance
     */
    public function setOperateEvent($operateEvent)
    {
        $this->operateEvent = $operateEvent;

        return $this;
    }

    /**
     * @return string
     */
    public function getOperateEvent()
    {
        return $this->operateEvent;
    }

    /**
     * @title      setBeforeOperateEvent
     * @description
     * @createtime 2020/5/27 4:23 下午
     * @param $beforeOperateEvent
     * @return $this
     * @author     yangyuance
     */
    public function setBeforeOperateEvent($beforeOperateEvent)
    {
        $this->beforeOperateEvent = $beforeOperateEvent;

        return $this;
    }

    /**
     * @return string
     */
    public function getBeforeOperateEvent()
    {
        return $this->beforeOperateEvent;
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
        $this->setData($this->setDataField((array) $this->getData()));

        $name = explode("\\", get_class($this));

        ThinkerAdmin::script(TemplateList::form()->TreeJs()->templates([
            strtolower($name[count($name) - 1]), $this
        ])->render());

        $_Value = join(",", (array) $this->getValue());

        return $this->getLabel() . "\n" .
            "<div class=\"".$this->getClassString()."\">\n" .
            "    <input type=\"hidden\" name=\"".$this->getField()."\" id=\"".$this->getId()."_input\" lay-filter=\"".$this->getId()."_input\" value=\"".$_Value."\" />\n" .
            "    <div id=\"".$this->getId()."\" lay-filter=\"".$this->getId()."\" ".$this->getAttrString()." ></div>\n" .
            "</div>";
    }
}