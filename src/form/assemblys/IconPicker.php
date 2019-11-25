<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午5:58
 */

namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class IconPicker
 * @package Yirius\Admin\form\assemblys
 * @method IconPicker type($type) 可以选择layui_icon，或者fontawsome(需要引入css)
 * @method IconPicker search(bool $search) 是否搜索
 * @method IconPicker page(bool $page) 是否分页
 * @method IconPicker limit(int $limit) 分页数量
 */
class IconPicker extends Assembly
{
    protected function _init()
    {
        ThinkerAdmin::script("iconplus", false, true);
    }

    protected $clickEvent = '';

    /**
     * @title      setClickEvent
     * @description
     * @createtime 2019/11/25 6:42 下午
     * @param $clickEvent
     * @return $this
     * @author     yangyuance
     */
    public function setClickEvent($clickEvent)
    {
        $this->clickEvent = $clickEvent;

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
        $jsonConfig = json_encode($this->getConfig());

        ThinkerAdmin::script(<<<HTML
layui.iconplus.render($.extend({
    // 选择器，推荐使用input
    elem: '#{$this->getId()}', //选择器ID
    // 数据类型：fontClass/layui_icon，
    type: 'layui_icon',
    // 是否开启搜索：true/false
    search: true,
    // 是否开启分页
    page: true,
    // 每页显示数量，默认12
    limit: 12,
    // 点击回调
    click: function (data) {
        //console.log(data);
        $("#{$this->getId()}_input").val(data.icon);
        
        {$this->clickEvent}
    }
}, {$jsonConfig}));
HTML
        );
        return <<<HTML
{$this->getLabel()}
<div class="{$this->getClass()}">
    <input type="hidden" name="{$this->getField()}" id="{$this->getId()}_input"/>
    <div id="{$this->getId()}" lay-filter="{$this->getId()}" value="{$this->getValue()}" {$this->getAttrs()}></div>
</div>
HTML;
    }
}