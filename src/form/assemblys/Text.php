<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午5:58
 */

namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

class Text extends Assembly
{
    /**
     * input's type
     * @var string
     */
    protected $inputType = "text";

    /**
     * @var string
     */
    protected $placeholder = '';

    /**
     * @var array
     */
    protected $inputClass = ['layui-input'];

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
        ThinkerAdmin::script(<<<HTML
$(document).off('{$event}', '#{$this->getId()}').on('{$event}', '#{$this->getId()}', function() {
    {$callback}
});
HTML
        );

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
    <input type="{$this->inputType}" class="{$this->getInputClass()}" name="{$this->getField()}" id="{$this->getId()}" lay-filter="{$this->getId()}" value="{$this->getValue()}" placeholder="{$this->placeholder}" {$this->getAttrs()} />
</div>
HTML;
    }

    /**
     * @title setPlaceholder
     * @description
     * @createtime 2019/2/24 下午6:39
     * @param $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @title setInputClass
     * @description
     * @createtime 2019/2/24 下午6:41
     * @param string $inputClass
     * @return $this
     */
    public function setInputClass($inputClass)
    {
        $this->inputClass[] = $inputClass;

        return $this;
    }

    /**
     * @return array
     */
    public function getInputClass()
    {
        return join(" ", $this->inputClass);
    }
}