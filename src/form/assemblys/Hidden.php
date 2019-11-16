<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/4
 * Time: 下午8:01
 */

namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

class Hidden extends Assembly
{
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
     * @description
     * @createtime 2019/3/10 下午4:18
     * @return mixed|string
     */
    public function render()
    {
        return <<<HTML
<input type="hidden" name="{$this->getField()}" id="{$this->getId()}" lay-filter="{$this->getId()}" value="{$this->getValue()}" {$this->getAttrs()} />
HTML;
    }
}