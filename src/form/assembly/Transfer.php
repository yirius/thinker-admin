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
 * Class Transfer
 *
 * @method Transfer setData(array $data)
 * @method Transfer setTitle(array $title)
 * @method Transfer setSpread(array $spread)
 * @method Transfer setDisabled(array $disabled)
 * @method Transfer setShowSearch(bool $showSearch)
 * @method Transfer setWidth(int $width)
 * @method Transfer setHeight(int $height)
 *
 * @method Transfer getData()
 * @method Transfer getTitle()
 * @method Transfer getSpread()
 * @method Transfer getDisabled()
 * @method Transfer getShowSearch()
 * @method Transfer getWidth()
 * @method Transfer getHeight()
 *
 * @package Yirius\Admin\form\assembly
 */
class Transfer extends Assembly
{
    /**
     * @var array
     */
    protected $config = [
        'data' => [],
        'value' => [],
        'spread' => [],
        'disabled' => []
    ];

    protected $onChange = '';

    /**
     * @title setOnChange
     * @description
     * @createtime 2019/6/12 4:23 PM
     * @param $onChange
     * @return Transfer
     */
    public function setOnChange($onChange)
    {
        $this->onChange = $onChange;

        return $this;
    }

    /**
     * @return string
     */
    public function getOnChange()
    {
        return $this->onChange;
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
var currentEleTransfer = document.querySelector("#{$this->getId()}"), 
    currentTransferInput = layui.jquery("#{$this->getId()}_input");
currentEleTransfer.transfer = layui.transfer.render($.extend({
    elem: "#{$this->getId()}",
    id: "{$this->getId()}",
    onchange: function(data, index){
        var checkedData = [];
        layui.each(layui.transfer.getData('{$this->getId()}'), function(n, v){
            checkedData.push(v.value);
        });
        currentTransferInput.val(checkedData.join(","));
        
        {$this->onChange}
    }
}, {$this->getConfig()}));
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
        Admin::script('transfer', 2);
    }

    /**
     * @title setConfig
     * @description
     * @createtime 2019/2/28 下午5:41
     * @param $config
     * @return Transfer
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
     * @return Transfer
     */
    public function setValue($value)
    {
        $this->config['value'] = is_array($value) ? $value : explode(",", $value);

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
            if(in_array($v['value'], $this->config['spread'])){
                if(!empty($v['children'])) {
                    $data[$i]["spread"] = true;
                }
            }
            if(in_array($v['value'], $this->config['disabled'])){
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