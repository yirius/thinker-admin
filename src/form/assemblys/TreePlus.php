<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/28
 * Time: 下午5:11
 */

namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class Tree
 *
 * @method TreePlus setData(array $data);
 * @method TreePlus setShowCheckbox(bool $isShow)
 * @method TreePlus setEdit(bool|array $edit)
 * @method TreePlus setAccordion(bool $isAccordion)
 * @method TreePlus setOnlyIconControl(bool $isOnlyIconControl)
 * @method TreePlus setIsJump(bool $isIsJump)
 * @method TreePlus setShowLine(bool $isShowLine)
 * @method TreePlus setSpread(array $spread)
 * @method TreePlus setDisabled(array $disabled)
 *
 * @method TreePlus getData();
 * @method TreePlus getShowCheckbox()
 * @method TreePlus getEdit()
 * @method TreePlus getAccordion()
 * @method TreePlus getOnlyIconControl()
 * @method TreePlus getIsJump()
 * @method TreePlus getShowLine()
 * @method TreePlus getSpread()
 * @method TreePlus getDisabled()
 *
 * @package Yirius\Admin\form\assembly
 */
class TreePlus extends Assembly
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

    protected $beforeOperateEvent = '';

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
     * @title      setBeforeOperateEvent
     * @description
     * @createtime 2019/11/20 2:31 下午
     * @param $beforeOperateEvent
     * @return $this
     * @author     yangyuance
     */
    public function setBeforeOperateEvent($beforeOperateEvent)
    {
        $this->beforeOperateEvent = $beforeOperateEvent;

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

        $jsonConfig = json_encode($this->getConfig());

        //add script
        ThinkerAdmin::script(<<<HTML
(function(){
var currentEleTree = document.querySelector("#{$this->getId()}"), isLoaded = false, currentTreeInput = layui.jquery("#{$this->getId()}_input");
currentEleTree.treeplus = layui.treeplus.render($.extend({
    elem: "#{$this->getId()}",
    id: "{$this->getId()}",
    click: function(obj){
        {$this->clickEvent}
    },
    oncheck: function(obj){
        if(isLoaded){
            this.checked = [];
            this._eachChecked(layui.treeplus.getChecked('{$this->getId()}'));
            console.log(currentTreeInput);
            console.log(this.checked.join(","));
            currentTreeInput.val(this.checked.join(","));
            {$this->checkedEvent}
        }
    },
    beforeOperate: function(type, obj){
        {$this->beforeOperateEvent}
    },
    operate: function(obj){
        {$this->operateEvent}
    },
    _eachChecked: function(data){
        var _this = this;
        layui.each(data, function(n, v){
            _this.checked.push(v.id);
            if(v.children && v.children.length !== 0){
                _this._eachChecked(v.children);
            }
        });
    }
}, {$jsonConfig}));
//防止错误触发
isLoaded = true;
layui.form.render();
})();
HTML
        );

        return <<<HTML
{$this->getLabel()}
<div class="{$this->getClass()}">
    <input type="hidden" name="{$this->getField()}" id="{$this->getId()}_input" lay-filter="{$this->getId()}_input" value="{$this->getValue()}" />
    <div id="{$this->getId()}" lay-filter="{$this->getId()}" {$this->getAttrs()} ></div>
</div>
HTML;
    }

    /**
     * @title afterSetForm
     * @description
     * @createtime 2019/2/24 下午11:28
     */
    protected function _init()
    {
        ThinkerAdmin::script('treeplus', false, true);
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
}