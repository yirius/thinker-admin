<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/4
 * Time: 下午8:01
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\form\Assembly;

class Hidden extends Assembly
{
    public function render()
    {
        return <<<HTML
<input type="hidden" class="{$this->getInputClass()}" name="{$this->getName()}" id="{$this->getId()}" lay-filter="{$this->getId()}" value="{$this->getValue()}" {$this->getAttributes()} />
HTML;
    }
}