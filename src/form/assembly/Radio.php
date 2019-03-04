<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午8:32
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\Admin;
use Yirius\Admin\form\Assembly;

class Radio extends Assembly
{
    /**
     * input's type
     * @var string
     */
    protected $inputType = "radio";

    /**
     * @var array
     */
    protected $optionsArray = [];


    /**
     * @title on
     * @description
     * @createtime 2019/3/3 下午9:44
     * @param $callback
     * @return $this
     */
    public function on($callback)
    {
        Admin::script(<<<HTML
layui.form.on("{$this->inputType}({$this->getId()})", function(obj){
{$callback}
});
HTML
        );
        return $this;
    }

    /**
     * @title options
     * @description
     * @createtime 2019/2/24 下午8:38
     * @param array $optionsArray
     * @return $this
     */
    public function options(array $optionsArray)
    {
        $this->optionsArray = $optionsArray;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $result = [];

        $attrs = $this->getAttributes();

        foreach ($this->optionsArray as $i => $v) {
            $result[] = '<input type="' . $this->inputType . '" value="' . $v['value'] . '" title="' . $v['text'] . '" name="' . $this->getName() . '" id="' . $this->getId() . $i . '" lay-filter="' . $this->getId() . $i . '" ' . $attrs . $this->checkValue($v) . (!empty($v['disabled']) ? ' disabled ' : '') . ' />';
        }

        return join("", $result);
    }

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        return <<<HTML
<label class="layui-form-label">{$this->getLabel()}</label>
<div class="{$this->getClass()}">
    {$this->getOptions()}
</div>
HTML;
    }

    /**
     * @title checkValue
     * @description base value checked
     * @createtime 2019/2/28 上午11:36
     * @param array $option
     * @return string
     */
    protected function checkValue(array $option)
    {
        if (!empty($option['checked'])) {
            return "checked='checked'";
        } else {
            if (!is_array($this->value)) {
                if($this->value == "" || $this->value == null){
                    $this->value = [];
                }else{
                    $this->value = explode(",", $this->value);
                }
            }
            //check value
            if (in_array($option['value'], $this->value)) {
                return "checked='checked'";
            } else {
                return "";
            }
        }
    }
}