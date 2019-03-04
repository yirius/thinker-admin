<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/28
 * Time: 下午5:11
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\Admin;
use Yirius\Admin\form\Assembly;

/**
 * Class Tree
 *
 * @method Tree setData(array $data);
 * @method Tree setEmptText($text);
 * @method Tree setUrl($url);
 * @method Tree setRenderAfterExpand(bool $bool);
 * @method Tree setHighlightCurrent(bool $bool);
 * @method Tree setDefaultExpandAll(bool $bool);
 * @method Tree setExpandOnClickNode(bool $bool);
 * @method Tree setCheckOnClickNode(bool $bool);
 * @method Tree setDefaultExpandedKeys(array $expendKey);
 * @method Tree setAutoExpandParent(bool $bool);
 * @method Tree setShowCheckbox(bool $bool);
 * @method Tree setCheckStrictly(bool $bool);
 * @method Tree setDefaultCheckedKeys(array $checkKeys);
 * @method Tree setccordion(bool $bool);
 * @method Tree setIndent(int $indent);
 * @method Tree setLazy(bool $bool);
 * @method Tree setDraggable(bool $bool);
 * @method Tree setContextmenuList(array $list);
 *
 * @method Tree getData();
 * @method Tree getEmptText();
 * @method Tree getUrl();
 * @method Tree getRenderAfterExpand();
 * @method Tree getHighlightCurrent();
 * @method Tree getDefaultExpandAll();
 * @method Tree getExpandOnClickNode();
 * @method Tree getCheckOnClickNode();
 * @method Tree getDefaultExpandedKeys();
 * @method Tree getAutoExpandParent();
 * @method Tree getShowCheckbox();
 * @method Tree getCheckStrictly();
 * @method Tree getDefaultCheckedKeys();
 * @method Tree getccordion();
 * @method Tree getIndent();
 * @method Tree getLazy();
 * @method Tree getDraggable();
 * @method Tree getContextmenuList();
 *
 * @package Yirius\Admin\form\assembly
 */
class Tree extends Assembly
{
    /**
     * @var string
     */
    protected $event = '';

    /**
     * @var string
     */
    protected $checkedEvent = '';

    /**
     * @var array
     */
    protected $config = [
        'renderAfterExpand' => false,
        'showCheckbox' => true
    ];

    /**
     * @title click
     * @description
     * @createtime 2019/2/28 下午5:28
     * @param $callback
     * @return $this
     */
    public function click($callback)
    {
       $this->event = <<<HTML
layui.eleTree.on("nodeClick({$this->getId()})",function(d) {
    {$callback}
});
HTML
        ;

        return $this;
    }

    /**
     * @title checked
     * @description
     * @createtime 2019/2/28 下午5:31
     * @param $callback
     * @return $this
     */
    public function checked($callback)
    {
        $this->checkedEvent = $callback;

        return $this;
    }

    /**
     * @title contextmenu
     * @description
     * @createtime 2019/2/28 下午5:31
     * @param $callback
     * @return $this
     */
    public function contextmenu($callback)
    {
        $this->event = <<<HTML
layui.eleTree.on("nodeContextmenu({$this->getId()})",function(d) {
    {$callback}
});
HTML
        ;

        return $this;
    }

    /**
     * @title drag
     * @description
     * @createtime 2019/2/28 下午5:31
     * @param $callback
     * @return $this
     */
    public function drag($callback)
    {
        $this->event = <<<HTML
layui.eleTree.on("nodeDrag({$this->getId()})",function(d) {
    {$callback}
});
HTML
        ;

        return $this;
    }

    /**
     * @title append
     * @description
     * @createtime 2019/2/28 下午5:31
     * @param $callback
     * @return $this
     */
    public function append($callback)
    {
        $this->event = <<<HTML
layui.eleTree.on("nodeAppend({$this->getId()})",function(d) {
    {$callback}
});
HTML
        ;

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
        //add script
        Admin::script(<<<HTML
(function(){
var currentEleTree = document.querySelector("#{$this->getId()}");
currentEleTree.eleTree = layui.eleTree.render($.extend({
    elem: "#{$this->getId()}"
}, {$this->getConfig()}));
layui.eleTree.on("nodeChecked({$this->getId()})",function(d) {
    //add checkEvent
    var _checked = [];
    layui.each(currentEleTree.eleTree.getChecked(false, true), function(n, v){
        _checked.push(v.value);
    });
    $("#{$this->getId()}_input").val(_checked.join(","));
    {$this->checkedEvent}
});
{$this->event}
})();
HTML
        );

        return <<<HTML
<label class="layui-form-label">{$this->getLabel()}</label>
<div class="{$this->getClass()}">
    <input type="hidden" name="{$this->getName()}" id="{$this->getId()}_input" lay-filter="{$this->getId()}_input" value="{$this->getValue()}" />
    <div class="eleTree" id="{$this->getId()}" lay-filter="{$this->getId()}" {$this->getAttributes()} ></div>
</div>
HTML;
    }

    /**
     * @title afterSetForm
     * @description
     * @createtime 2019/2/24 下午11:28
     */
    protected function afterSetForm()
    {
        Admin::script('eleTree', 2);

        Admin::style('eleTree', 1);
    }

    /**
     * @title setConfig
     * @description
     * @createtime 2019/2/28 下午5:41
     * @param $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return json_encode($this->config);
    }

    /**
     * @title setValue
     * @description
     * @createtime 2019/2/28 下午6:12
     * @param string|array $value
     * @return $this|Assembly
     */
    public function setValue($value)
    {
        $this->value = is_array($value) ? join(",", $value) : $value;

        $this->setDefaultCheckedKeys(is_array($value) ? $value : explode(",", $value));

        return $this;
    }

    /**
     * @title __call
     * @description
     * @createtime 2019/2/26 下午5:25
     * @param $name
     * @param $arguments
     * @return $this|mixed|string
     */
    public function __call($name, $arguments)
    {
        $operateType = substr($name, 0, 3);
        $firstChar = substr($name, 3, 1);
        $name = strtolower($firstChar) . substr($name, 4);

        //if it is set
        if($operateType === "set"){

            $this->config[$name] = $arguments[0];

            return $this;

        }else if($operateType === "get"){

            return empty($this->config[$name]) ? '' : $this->config[$name];

        }

        return $this;
    }
}