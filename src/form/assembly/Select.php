<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午6:49
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\form\Assembly;

class Select extends Assembly
{
    /**
     * @var string
     */
    protected $class = 'layui-input-block';

    /**
     * @var string
     */
    protected $placeholder = '';

    /**
     * @var array
     */
    protected $inputClass = ['layui-input'];

    /**
     * @var array
     */
    protected $optionsArray = [];

    /**
     * @title setPlaceholder
     * @description
     * @createtime 2019/2/24 下午6:39
     * @param $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        if(empty($this->placeholder)){
            return '';
        }else{
            return '<option value="">' . $this->placeholder . '</option>';
        }
    }

    /**
     * @title setInputClass
     * @description
     * @createtime 2019/2/24 下午6:41
     * @param string $inputClass
     * @return $this
     */
    public function setSelectClass($inputClass)
    {
        $this->inputClass[] = $inputClass;

        return $this;
    }

    /**
     * @return array
     */
    public function getSelectClass()
    {
        return join(" ", $this->inputClass);
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

        foreach($this->optionsArray as $i => $v){
            if(empty($v['list'])){
                $result[] = '<option value="'. $v['value'] .'">'. $v['text'] . $this->checkValue($v) . '</option>';
            }else{
                $temp = [];
                $temp[] = '<optgroup label="'. $v['text'] .'">';
                foreach($v['list'] as $j => $val){
                    $temp[] = '<option value="'. $val['value'] . $this->checkValue($val) . '">'. $val['text'] .'</option>';
                }
                $temp[] = '</optgroup>';
                $result[] = join("", $temp);
            }
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
    <select class="{$this->getSelectClass()}" name="{$this->getName()}" id="{$this->getId()}" lay-filter="{$this->getId()}" {$this->getAttributes()} >
        {$this->getPlaceholder()}
        {$this->getOptions()}
    </select>
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
        if(!empty($option['checked'])){
            return "selected='selected'";
        }else{
            if(!is_array($this->value)){
                $this->value = explode(",", $this->value);
            }
            //check value
            if(in_array($option['value'], $this->value)){
                return "selected='selected'";
            }else{
                return "";
            }
        }
    }
}