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
    protected $class = ["layui-btn"];

    /**
     * @title primary
     * @description set btn class to primary
     * @createtime 2019/2/24 下午3:59
     * @return $this
     */
    public function primary()
    {
        $this->setClass('layui-btn-primary');

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
        $this->setClass('layui-btn-normal');

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
        $this->setClass('layui-btn-warm');

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
        $this->setClass('layui-btn-danger');

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
        $this->setClass('layui-btn-disabled');

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
        $this->setClass('layui-btn-lg');

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
        $this->setClass('layui-btn-sm');

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
        $this->setClass('layui-btn-xs');

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
        $this->setClass('layui-btn-radius');

        return $this;
    }

    /**
     * @title on
     * @description add eventlister on
     * @createtime 2019/2/24 下午4:39
     * @param $event
     * @param $callback
     */
    public function on($event, $callback){
        Admin::script(<<<HTML
$('#{$this->getId()}').on('{$event}', function() {
    {$callback}
});
HTML
        );
    }

    /**
     * @title render
     * @description
     * @createtime 2019/2/24 下午3:59
     * @return mixed|string
     */
    public function render()
    {
        return <<<HTML
<button class="{$this->getClass()}" id="{$this->getId()}" lay-filter="{$this->getId()}" name="{$this->getName()}" {$this->getAttributes()}>{$this->getLabel()}</button>
HTML
            ;
    }
}