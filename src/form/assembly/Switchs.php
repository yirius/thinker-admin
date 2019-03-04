<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午8:43
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\Admin;
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
     * @title on
     * @description
     * @createtime 2019/3/3 下午9:44
     * @param $callback
     * @return $this
     */
    public function on($callback)
    {
        Admin::script(<<<HTML
layui.form.on("switch({$this->getId()})", function(obj){
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
        $checked = empty($this->value) ? '' : "checked='checked'";

        return <<<HTML
<label class="layui-form-label">{$this->getLabel()}</label>
<div class="{$this->getClass()}">
    <input type="checkbox" name="{$this->getName()}" id="{$this->getId()}" lay-filter="{$this->getId()}" lay-skin="switch" lay-text="{$this->text}" value="1" {$checked} {$this->getAttributes()}>
</div>
HTML;
    }
}