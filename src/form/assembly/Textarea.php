<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午5:58
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\form\Assembly;

class Textarea extends Assembly
{
    /**
     * @var string
     */
    protected $class = 'layui-input-block';

    /**
     * @var string
     */
    protected $placeholder = '';

    /**
     * @var array
     */
    protected $inputClass = ['layui-textarea'];

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

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        return <<<HTML
<label class="layui-form-label">{$this->getLabel()}</label>
<div class="{$this->getClass()}">
    <textarea class="{$this->getInputClass()}" name="{$this->getName()}" id="{$this->getId()}" lay-filter="{$this->getId()}" value="{$this->getValue()}" {$this->getAttributes()} >{$this->placeholder}</textarea>
</div>
HTML;
    }
}