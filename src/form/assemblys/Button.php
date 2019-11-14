<?php


namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\form\Assembly;

/**
 * Class Button
 * @package Yirius\Admin\form\assemblys
 * @method Button primary();
 * @method Button normal();
 * @method Button warm();
 * @method Button danger();
 * @method Button disabled();
 * @method Button lg();
 * @method Button sm();
 * @method Button xs();
 * @method Button radius();
 */
class Button extends Assembly
{
    /**
     * @var array
     */
    protected $class = ['layui-btn'];

    /**
     * @title       render
     * @description 每一个组件需要继承渲染接口
     * @createtime  2019/11/14 4:26 下午
     * @return string
     * @author      yangyuance
     */
    public function render()
    {
        return <<<HTML
<button class="{$this->getClass()}" id="{$this->getId()}" lay-filter="{$this->getId()}" name="{$this->getField()}" {$this->getAttrs()}>{$this->getText()}</button>
HTML
            ;
    }

    /**
     * @title      __call
     * @description 重写一下call方法
     * @createtime 2019/11/14 5:41 下午
     * @param $name
     * @param $arguments
     * @return mixed|string
     * @author     yangyuance
     */
    public function __call($name, $arguments)
    {
        //一些默认操作，不需要重写了
        if(in_array($name, ['primary', 'normal', 'warm', 'danger', 'disabled', 'lg', 'sm', 'xs', 'radius'])){
            $this->setClass("layui-btn-" . $name);
            return $this;
        }

        return parent::__call($name, $arguments);
    }
}