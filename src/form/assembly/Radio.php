<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午8:32
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\form\Assembly;

class Radio extends Assembly
{
    /**
     * input's type
     * @var string
     */
    protected $inputType = "radio";

    /**
     * @var string
     */
    protected $class = 'layui-input-block';

    /**
     * @var array
     */
    protected $optionsArray = [];

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

        foreach($this->optionsArray as $i => $v){
            $result[] = '<input type="'. $this->inputType .'" value="'. $v['value'] .'" title="'. $v['text'] .'" name="'. $this->getName() .'" id="'. $this->getId() . $i .'" lay-filter="'. $this->getId() . $i .'" '. $attrs .' />';
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
}