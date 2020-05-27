<?php


namespace Yirius\Admin\renders\form;


use Yirius\Admin\support\abstracts\LayoutAbstract;

/**
 * Class Assembly
 * @package Yirius\Admin\renders\form
 * @method Assembly setValue($value);
 * @method Assembly getValue();
 *
 * @method Assembly setField($field);
 * @method Assembly getField();
 */
abstract class Assembly extends LayoutAbstract
{
    protected $thinkerAssemblys;

    protected $text = "";

    /**
     * @title      setText
     * @description
     * @createtime 2020/5/27 2:45 下午
     * @param $text
     * @return $this
     * @author     yangyuance
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    public function __construct($field, $text)
    {
        parent::__construct();

        //重新渲染一下
        $this->configsFields = array_merge([
            "field", "value"
        ], $this->configsFields);

        //设置参数
        $this->setField($field)->setText($text)
            ->setTrimId("thinker_" . $field)->addClass("layui-input-block");
    }

    /**
     * @title      setThinkerAssemblys
     * @description
     * @createtime 2020/5/27 2:44 下午
     * @param ThinkerAssemblys $thinkerAssemblys
     * @return $this
     * @author     yangyuance
     */
    public function setThinkerAssemblys(ThinkerAssemblys $thinkerAssemblys)
    {
        $this->thinkerAssemblys = $thinkerAssemblys;

        $this->setTrimId($thinkerAssemblys->getId() . "_" . $this->getField());

        return $this;
    }

    /**
     * @return ThinkerAssemblys
     */
    public function getThinkerAssemblys()
    {
        return $this->thinkerAssemblys;
    }

    /**
     * @title       getLabel
     * @description 获取label内容
     * @createtime  2020/5/27 2:46 下午
     * @return string
     * @author      yangyuance
     */
    public function getLabel()
    {
        if ($this->text == "") {
            $this->removeClass("layui-input-block")->addClass("layui-input-inline");

            return "";
        } else {
            return "<label class=\"layui-form-label\">" . $this->text . "</label>";
        }
    }

    /**
     * @title      getValueString
     * @description
     * @createtime 2020/5/27 2:47 下午
     * @return string|Assembly
     * @author     yangyuance
     */
    public function getValueString()
    {
        return empty($this->getValue()) ? "" : $this->getValue();
    }

    private $_tempValues = [];

    /**
     * @title      parseIsChecked
     * @description 检查是否选中
     * @createtime 2020/5/27 3:14 下午
     * @param array $textValue
     * @return bool
     * @author     yangyuance
     */
    protected function parseIsChecked(array $textValue)
    {
        if (!empty($textValue['checked'])) {
            return true;
        } else {
            //记录参数
            if (empty($this->_tempValues)) {
                $value = $this->getValue();
                if (!empty($value)) {
                    try {
                        if (is_string($value)) {
                            $this->_tempValues = explode(",", $value);
                        } else if (is_array($value)) {
                            $this->_tempValues = $value;
                        }
                    } catch (\Exception $e) {
                        thinker_error($e);
                    }
                }
            }

            if (!empty($this->_tempValues) && !empty($textValue['value']) &&
                in_array($textValue['value'], $this->_tempValues)
            ) {
                return true;
            }

            return false;
        }
    }
}