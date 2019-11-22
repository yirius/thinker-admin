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

    protected $notUseValue = 0;

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
     * @title      notUseValue
     * @description
     * @createtime 2019/11/20 3:33 下午
     * @param $value
     * @return $this
     * @author     yangyuance
     */
    public function notUseValue($value)
    {
        $this->notUseValue = $value;

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
        ThinkerAdmin::script(<<<HTML
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
        $checked = "";
        if($this->getValue() == $this->useValue){
            $checked = "checked='checked'";
        }

        return <<<HTML
{$this->getLabel()}
<div class="{$this->getClass()}">
    <input type="checkbox" lay-filter="{$this->getId()}" lay-skin="switch" lay-text="{$this->filltext}" name="{$this->getField()}" value="{$this->useValue}" data-notuse="{$this->notUseValue}" id="{$this->getId()}" {$checked} {$this->getAttrs()}>
</div>
HTML;
    }
}