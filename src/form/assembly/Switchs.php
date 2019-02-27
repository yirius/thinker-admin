<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午8:43
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\form\Assembly;

class Switchs extends Assembly
{
    protected $text = '开|关';

    /**
     * @title text
     * @description
     * @createtime 2019/2/24 下午9:03
     * @param $on
     * @param $off
     * @return $this
     */
    public function text($on, $off)
    {
        $this->text = $on . "|" . $off;

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
<label class="layui-form-label">{$this->getLabel()}</label>
<div class="{$this->getClass()}">
    <input type="checkbox" name="{$this->getName()}" id="{$this->getId()}" lay-filter="{$this->getId()}" lay-skin="switch" lay-text="{$this->text}" value="1" {$this->getAttributes()}>
</div>
HTML;
    }
}