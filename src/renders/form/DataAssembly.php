<?php


namespace Yirius\Admin\renders\form;

/**
 * Class DataAssembly
 * @package Yirius\Admin\renders\form
 * @method Assembly setSpread(array $value);
 * @method Assembly setDisabled(array $value);
 * @method Assembly setData(array $value);
 *
 * @method Assembly getSpread();
 * @method Assembly getDisabled();
 * @method Assembly getData();
 */
abstract class DataAssembly extends Assembly
{
    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        //重新渲染一下
        $this->configsFields = array_merge([
            "spread", "disabled", "data"
        ], $this->configsFields);
    }

    private $listValues = [];

    /**
     * @title      setDataField
     * @description
     * @createtime 2020/5/27 4:15 下午
     * @param array $textValues
     * @author     yangyuance
     */
    protected function setDataField(array $textValues) {
        if(empty($this->listValues)) {
            if(!empty($this->getValue())) {
                if(is_string($this->getValue())) {
                    $this->listValues = explode(",", $this->getValue());
                } else {
                    $this->listValues = $this->getValue();
                }
            }
        }

        $spread = $this->getSpread();
        if(!is_array($spread)) $spread = explode(",", $spread);

        $disabled = $this->getDisabled();
        if(!is_array($disabled)) $disabled = explode(",", $disabled);

        foreach ($textValues as $i => $textValue) {
            if(!empty($textValue['value'])) {
                if(in_array($textValue['value'], $this->listValues)) {
                    $textValue['checked'] = true;
                }
                if(in_array($textValue['value'], $spread)) {
                    $textValue['spread'] = true;
                }
                if(in_array($textValue['value'], $disabled)) {
                    $textValue['disabled'] = true;
                }
            }

            if(!empty($textValue['childs'])) {
                $this->setDataField($textValue['childs']);
            }
        }
    }
}