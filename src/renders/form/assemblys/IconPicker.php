<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;
use Yirius\Admin\templates\TemplateList;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class IconPicker
 * @package Yirius\Admin\renders\form\assemblys
 * @method IconPicker type($type) 可以选择layui_icon，或者fontawsome(需要引入css)
 * @method IconPicker search(bool $search) 是否搜索
 * @method IconPicker page(bool $page) 是否分页
 * @method IconPicker limit(int $limit) 分页数量
 */
class IconPicker extends Assembly
{
    public $configsFields = ["type", "search", "page", "limit"];

    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        ThinkerAdmin::script("iconplus", false, true);
    }

    private $clickEvent = "";

    /**
     * @title      setClickEvent
     * @description
     * @createtime 2020/5/27 3:37 下午
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
     * @title       render
     * @description 每一个组件需要继承渲染接口
     * @createtime  2020/5/27 1:58 下午
     * @return string
     * @author      yangyuance
     */
    public function render()
    {
        ThinkerAdmin::script(TemplateList::form()->IconPickerJs()->templates([
            $this->getId(), $this->getId() . "_input", $this->clickEvent, $this->getConfigString()
        ])->render());

        return $this->getLabel() . "\n" .
            "<div class=\"".$this->getClassString()."\">\n" .
            "    <input type=\"hidden\" name=\"".$this->getField()."\" id=\"".$this->getId()."_input\"/>\n" .
            "    <div id=\"".$this->getId()."\" lay-filter=\"".$this->getId()."\" value=\"".$this->getValueString()."\" ".$this->getAttrString()."></div>\n" .
            "</div>";
    }
}