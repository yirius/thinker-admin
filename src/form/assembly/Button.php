<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 下午4:44
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\Admin;
use Yirius\Admin\form\Assembly;

class Button extends Assembly
{
    /**
     * @var array
     */
    protected $buttonClass = ['layui-btn'];

    /**
     * @var string
     */
    protected $text = '';

    /**
     * @var string
     */
    protected $buttonHtml = '';

    /**
     * @var bool
     */
    protected $haveLabel = true;

    /**
     * @title primary
     * @description set btn class to primary
     * @createtime 2019/2/24 下午3:59
     * @return $this
     */
    public function primary()
    {
        $this->setButtonClass('layui-btn-primary');

        return $this;
    }

    /**
     * @title normal
     * @description set btn class to normal
     * @createtime 2019/2/24 下午3:59
     * @return $this
     */
    public function normal()
    {
        $this->setButtonClass('layui-btn-normal');

        return $this;
    }

    /**
     * @title warm
     * @description set btn class to warm
     * @createtime 2019/2/24 下午3:59
     * @return $this
     */
    public function warm()
    {
        $this->setButtonClass('layui-btn-warm');

        return $this;
    }

    /**
     * @title danger
     * @description set btn class to danger
     * @createtime 2019/2/24 下午3:59
     * @return $this
     */
    public function danger()
    {
        $this->setButtonClass('layui-btn-danger');

        return $this;
    }

    /**
     * @title disabled
     * @description set btn class to disabled
     * @createtime 2019/2/24 下午3:59
     * @return $this
     */
    public function disabled()
    {
        $this->setButtonClass('layui-btn-disabled');

        return $this;
    }

    /**
     * @title lg
     * @description set btn class to lg
     * @createtime 2019/2/24 下午3:59
     * @return $this
     */
    public function lg()
    {
        $this->setButtonClass('layui-btn-lg');

        return $this;
    }

    /**
     * @title sm
     * @description set btn class to sm
     * @createtime 2019/2/24 下午3:59
     * @return $this
     */
    public function sm()
    {
        $this->setButtonClass('layui-btn-sm');

        return $this;
    }

    /**
     * @title xs
     * @description set btn class to xs
     * @createtime 2019/2/24 下午3:59
     * @return $this
     */
    public function xs()
    {
        $this->setButtonClass('layui-btn-xs');

        return $this;
    }

    /**
     * @title radius
     * @description set btn class to radius
     * @createtime 2019/2/24 下午3:59
     * @return $this
     */
    public function radius()
    {
        $this->setButtonClass('layui-btn-radius');

        return $this;
    }

    /**
     * @title on
     * @description
     * @createtime 2019/3/3 下午9:28
     * @param $event
     * @param $callback
     * @return $this
     */
    public function on($event, $callback)
    {
        Admin::script(<<<HTML
$(document).off('{$event}', '#{$this->getId()}').on('{$event}', '#{$this->getId()}', function() {
    {$callback}
});
HTML
        );

        return $this;
    }

    /**
     * @title render
     * @description
     * @createtime 2019/2/24 下午3:59
     * @return mixed|string
     */
    public function render()
    {
        $label = $this->haveLabel ? '<label class="layui-form-label">'. $this->getLabel() .'</label>' : '';

        return <<<HTML
{$label}
<div class="{$this->getClass()}">
    <button class="{$this->getButtonClass()}" id="{$this->getId()}" lay-filter="{$this->getId()}" name="{$this->getName()}" {$this->getAttributes()}>{$this->getText()}</button>
    {$this->getButtonHtml()}
</div>
HTML
            ;
    }

    /**
     * @title setButtonClass
     * @description
     * @createtime 2019/3/3 下午9:27
     * @param $buttonClass
     * @return $this
     */
    public function setButtonClass($buttonClass)
    {
        $this->buttonClass[] = $buttonClass;

        return $this;
    }

    /**
     * @return array
     */
    public function getButtonClass()
    {
        return join(" ", $this->buttonClass);
    }

    /**
     * @title setText
     * @description
     * @createtime 2019/3/3 下午9:29
     * @param $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @title setButtonHtml
     * @description
     * @createtime 2019/3/3 下午9:42
     * @param $buttonHtml
     * @return $this
     */
    public function setButtonHtml($buttonHtml)
    {
        $this->buttonHtml .= $buttonHtml;

        return $this;
    }

    /**
     * @return string
     */
    public function getButtonHtml()
    {
        return $this->buttonHtml;
    }

    /**
     * @title setHaveLabel
     * @description
     * @createtime 2019/3/4 下午12:54
     * @param bool $haveLabel
     * @return $this
     */
    public function setHaveLabel($haveLabel)
    {
        $this->haveLabel = $haveLabel;

        return $this;
    }
}