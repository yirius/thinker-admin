<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午8:43
 */

namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

class Switchs extends Assembly
{
    protected $isOn = false;

    protected $filltext = '开|关';

    /**
     * @title text
     * @description
     * @createtime 2019/2/24 下午9:03
     * @param $on
     * @param $off
     * @return $this
     */
    public function filltext($on, $off)
    {
        $this->text = $on . "|" . $off;

        return $this;
    }

    protected $useValue = 1;

    /**
     * @title      usevalue
     * @description
     * @createtime 2019/11/16 10:30 下午
     * @param $value
     * @return $this
     * @author     yangyuance
     */
    public function useValue($value)
    {
        $this->useValue = $value;

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
        $this->isOn = true;

        ThinkerAdmin::script(<<<HTML
layui.form.on("switch({$this->getId()})", function(obj){
$("#{$this->getId()}").val(obj.elem.checked ? 1 : 0);
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
        $checked = "";
        if($this->getValue() == $this->useValue){
            $checked = "checked='checked'";
        }

        if(!$this->isOn){
            $this->on("");
        }

        return <<<HTML
<label class="layui-form-label">{$this->getText()}</label>
<div class="{$this->getClass()}">
    <input type="checkbox" lay-filter="{$this->getId()}" lay-skin="switch" lay-text="{$this->filltext}" value="{$this->useValue}" {$checked} {$this->getAttrs()}>
    <input type="hidden" name="{$this->getField()}" value="{$this->useValue}" id="{$this->getId()}" />
</div>
HTML;
    }
}