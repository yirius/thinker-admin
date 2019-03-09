<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/9
 * Time: 下午11:22
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\Admin;
use Yirius\Admin\form\Assembly;

class ColorPicker extends Assembly
{
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
    <div name="{$this->getName()}" id="{$this->getId()}" lay-filter="{$this->getId()}" value="{$this->getValue()}" {$this->getAttributes()} lay-colorpicker=""></div>
</div>
HTML;
    }

    /**
     * @title afterSetForm
     * @description
     * @createtime 2019/3/9 下午11:25
     */
    protected function afterSetForm()
    {
        Admin::script("colorpicker", 2);
    }
}