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
 * @method Tree setShowCheckbox(bool $isShow)
 * @method Tree setEdit(bool|array $edit)
 * @method Tree setAccordion(bool $isAccordion)
 * @method Tree setOnlyIconControl(bool $isOnlyIconControl)
 * @method Tree setIsJump(bool $isIsJump)
 * @method Tree setShowLine(bool $isShowLine)
 * @method Tree setSpread(array $spread)
 * @method Tree setDisabled(array $disabled)
 *
 * @method Tree getData();
 * @method Tree getShowCheckbox()
 * @method Tree getEdit()
 * @method Tree getAccordion()
 * @method Tree getOnlyIconControl()
 * @method Tree getIsJump()
 * @method Tree getShowLine()
 * @method Tree getSpread()
 * @method Tree getDisabled()
 *
 * @package Yirius\Admin\form\assembly
 */
class Tree extends Assembly
{
    /**
     * @var array
     */
    protected $config = [
        'data' => [],
        'checked' => [],
        'spread' => [],
        'disabled' => []
    ];

    protected $clickEvent = '';

    protected $checkedEvent = '';

    protected $operateEvent = '';

    /**
     * @title setClickEvent
     * @description
     * @createtime 2019/6/12 3:41 PM
     * @param $clickEvent
     * @return $this
     */
    public function setClickEvent($clickEvent)
    {
        $this->clickEvent = $clickEvent;

        return $this;
    }

    /**
     * @title setOperateEvent
     * @description
     * @createtime 2019/6/12 3:41 PM
     * @param $operateEvent
     * @return $this
     */
    public function setOperateEvent($operateEvent)
    {
        $this->operateEvent = $operateEvent;

        return $this;
    }

    /**
     * @title setCheckedEvent
     * @description
     * @createtime 2019/6/12 3:41 PM
     * @param $checkedEvent
     * @return $this
     */
    public function setCheckedEvent($checkedEvent)
    {
        $this->checkedEvent = $checkedEvent;

        return $this;
    }

    /**
     * @return string
     */
    public function getClickEvent()
    {
        return $this->clickEvent;
    }

    /**
     * @return string
     */
    public function getCheckedEvent()
    {
        return $this->checkedEvent;
    }

    /**
     * @return string
     */
    public function getOperateEvent()
    {
        return $this->operateEvent;
    }

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        //运算一遍数据
        $this->setDataField($this->config['data']);

        //add script
        Admin::script(<<<HTML
(function(){
var currentEleTree = document.querySelector("#{$this->getId()}"), isLoaded = false, currentTreeInput = layui.jquery("#{$this->getId()}_input");
currentEleTree.tree = layui.tree.render($.extend({
    elem: "#{$this->getId()}",
    id: "{$this->getId()}",
    click: function(obj){
        {$this->clickEvent}
    },
    oncheck: function(obj){
        if(isLoaded){
            this.checked = [];
            this._eachChecked(layui.tree.getChecked('{$this->getId()}'));
            currentTreeInput.val(this.checked.join(","));
            {$this->checkedEvent}
        }
    },
    operate: function(obj){
        {$this->operateEvent}
    },
    _eachChecked: function(data){
        var _this = this;
        layui.each(data, function(n, v){
            _this.checked.push(v.id);
            if(v.children.length !== 0){
                _this._eachChecked(v.children);
            }
        });
    }
}, {$this->getConfig()}));
//防止错误触发
isLoaded = true;
})();
HTML
        );

        return <<<HTML
<label class="layui-form-label">{$this->getLabel()}</label>
<div class="{$this->getClass()}">
    <input type="hidden" name="{$this->getName()}" id="{$this->getId()}_input" lay-filter="{$this->getId()}_input" value="{$this->getValue()}" />
    <div id="{$this->getId()}" lay-filter="{$this->getId()}" {$this->getAttributes()} ></div>
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
        Admin::script('tree', 2);
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
        $this->config['checked'] = is_array($value) ? $value : explode(",", $value);

        $this->value = is_array($value) ? join(",", $value) : $value;

        return $this;
    }

    /**
     * @title setDataField
     * @description
     * @createtime 2019/6/12 3:17 PM
     * @param array $data
     */
    protected function setDataField(array &$data)
    {
        foreach($data as $i => $v){
            if(in_array($v['id'], $this->config['checked'])){
                if(empty($v['children'])) {
                    $data[$i]["checked"] = true;
                }
            }
            if(in_array($v['id'], $this->config['spread'])){
                if(!empty($v['children'])) {
                    $data[$i]["spread"] = true;
                }
            }
            if(in_array($v['id'], $this->config['disabled'])){
                $data[$i]["disabled"] = true;
            }
            if(!empty($v['children'])){
                $this->setDataField($data[$i]['children']);
            }
        }
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