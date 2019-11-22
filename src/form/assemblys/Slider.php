<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/9
 * Time: 下午11:22
 */

namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class Slider
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
 * @package Yirius\Admin\form\assembly
 */
class Slider extends Assembly
{
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
        $this->setAttrs("data-change", $callback);

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
        $this->setAttrs("data-set-tips", $callback);

        return $this;
    }

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        return <<<HTML
{$this->getLabel()}
<div class="{$this->getClass()}">
    <div id="{$this->getId()}" lay-filter="{$this->getId()}" data-value="{$this->getValue()}" {$this->getAttrs()} lay-slider=""></div>
    <input type="hidden" id="{$this->getId()}_hidden" name="{$this->getField()}" value="{$this->getValue()}"/>
</div>
HTML;
    }

    /**
     * @title afterSetForm
     * @description
     * @createtime 2019/3/9 下午11:25
     */
    protected function _init()
    {
        ThinkerAdmin::script("slider", false, true);
    }
}