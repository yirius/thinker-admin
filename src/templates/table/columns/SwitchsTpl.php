<?php


namespace Yirius\Admin\templates\table\columns;


use Yirius\Admin\templates\Templates;

class SwitchsTpl extends Templates
{
    protected $args = ["id", "config"];

    /**
     * @title      render
     * @description
     * @createtime 2020/5/26 9:18 下午
     * @return string
     * @author     yangyuance
     */
    public function render()
    {
        $config = $this->getConfig("config");

        return <<<HTML
<script type="text/html" id="{$this->getConfig("id")}">
    <input type="checkbox" name='{$config["filter"]}'
           value='{$config["checkedValue"]}'
           data-json="{{=JSON.stringify(d) }}"
           lay-skin="switch" lay-text="开|关"
           lay-filter='switch{$config["filter"]}'
           {{ d.{$config["field"]} == {$config["checkedValue"]} ? 'checked' : '' }}
    >
</script>
HTML;
    }
}