<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class Slider
 * @package Yirius\Admin\renders\form\assemblys
 *
 * @method Slider setType($type);
 * @method Slider setMin(int $min);
 * @method Slider setMax(int $max);
 * @method Slider setRange(bool $bool);
 * @method Slider setStep(int $step);
 * @method Slider setShowstep(bool $bool);
 * @method Slider setTips(bool $bool);
 * @method Slider setInput(bool $bool);
 * @method Slider setHeight(int $height);
 * @method Slider setDisabled(bool $bool);
 * @method Slider setTheme($theme);
 *
 * @method Slider getType();
 * @method Slider getMin();
 * @method Slider getMax();
 * @method Slider getRange();
 * @method Slider getStep();
 * @method Slider getShowstep();
 * @method Slider getTips();
 * @method Slider getInput();
 * @method Slider getHeight();
 * @method Slider getDisabled();
 * @method Slider getTheme();
 */
class Slider extends Assembly
{
    protected $attrsFields = [
        "type", "min", "max", "range", "step", "showstep", "tips", "input", "height", "disabled", "theme"
    ];

    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        ThinkerAdmin::script("slider", false, true);
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
    public function onSetTips($callback)
    {
        $this->addAttr("data-set-tips", $callback);

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
            "    <div id=\"".$this->getId()."\" lay-filter=\"".$this->getId()."\" data-value=\"".$this->getValueString()."\" ".$this->getAttrString()." lay-slider=\"\"></div>\n" .
            "    <input type=\"hidden\" id=\"".$this->getId()."_hidden\" name=\"".$this->getField()."\" value=\"".$this->getValueString()."\"/>\n" .
            "</div>";
    }
}