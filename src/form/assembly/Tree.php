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
 * @method Tree setRenderAfterExpand($bool = false);
 * @method Tree setHighlightCurrent($bool = true);
 * @method Tree setDefaultExpandAll($bool = true);
 * @method Tree setExpandOnClickNode($bool = false);
 * @method Tree setCheckOnClickNode($bool = true);
 * @method Tree setDefaultExpandedKeys(array $expendKey);
 * @method Tree setAutoExpandParent($bool = true);
 * @method Tree setShowCheckbox($bool = false);
 * @method Tree setCheckStrictly($bool = true);
 * @method Tree setDefaultCheckedKeys(array $checkKeys);
 * @method Tree setccordion($bool = false);
 * @method Tree setIndent(int $indent);
 * @method Tree setLazy($bool = true);
 * @method Tree setDraggable($bool = true);
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
        PRINT_R($this->getValue());

        //add script
        Admin::script(<<<HTML
var {$this->getEleName()} = layui.eleTree.render($.extend({
    elem: '#{$this->getId()}'
}, {$this->getConfig()}));
layui.eleTree.on("nodeChecked({$this->getId()})",function(d) {
    //add checkEvent
    var _checked = [];
    layui.each({$this->getEleName()}.getChecked(false, true), function(n, v){
        _checked.push(v.value);
    });
    $("#{$this->getId()}_input").val(_checked.join(","));
    {$this->checkedEvent}
});
{$this->event}
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
     * @title getEleName
     * @description
     * @createtime 2019/2/28 下午5:53
     * @return string
     */
    protected function getEleName()
    {
        return $this->getId() . "_eletree";
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