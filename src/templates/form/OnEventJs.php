<?php


namespace Yirius\Admin\templates\form;


use Yirius\Admin\templates\Templates;

class OnEventJs extends Templates
{
    protected $args = ["event", "id", "callback"];

    /**
     * @title      render
     * @description
     * @createtime 2020/5/26 9:18 下午
     * @return string
     * @author     yangyuance
     */
    public function render()
    {
        return <<<HTML
$(document).off('{$this->getConfig("event")}', '#{$this->getConfig("id")}').on('{$this->getConfig("event")}', '#{$this->getConfig("id")}', function() {
    {$this->getConfig("callback")}
});
HTML;
    }
}