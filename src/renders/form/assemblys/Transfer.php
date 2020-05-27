<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\DataAssembly;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class Transfer
 * @package Yirius\Admin\renders\form\assemblys
 * @method Transfer setShowSearch(bool $value);
 * @method Transfer setWidth(int $value);
 * @method Transfer setHeight(int $value);
 *
 * @method Transfer getShowSearch();
 * @method Transfer getWidth();
 * @method Transfer getHeight();
 */
class Transfer extends DataAssembly
{
    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        ThinkerAdmin::script("transfer", false, true);

        $this->configsFields = array_merge([
            "showSearch", "width", "height"
        ], $this->configsFields);
    }

    /**
     * @title       render
     * @description 每一个组件需要继承渲染接口
     * @createtime  2020/5/27 1:58 下午
     * @return string
     * @author      yangyuance
     */
    public function render()
    {
        $this->setDataField((array) $this->getData());

        ThinkerAdmin::script(";(function(){\n" .
            "var currentEleTransfer = document.querySelector(\"#".$this->getId()."\"), \n" .
            "    currentTransferInput = layui.jquery(\"#".$this->getId()."_input\");\n" .
            "currentEleTransfer.transfer = layui.transfer.render($.extend({\n" .
            "    elem: \"#".$this->getId()."\",\n" .
            "    id: \"".$this->getId()."\",\n" .
            "    onchange: function(data, index){\n" .
            "        var checkedData = [];\n" .
            "        layui.each(layui.transfer.$this->getData('".$this->getId()."'), function(n, v){\n" .
            "            checkedData.push(v.value);\n" .
            "        });\n" .
            "        currentTransferInput.val(checkedData.join(\",\"));\n" .
            "        \n" .
            "        ".onChange."\n" .
            "    }\n" .
            "}, ".$this->getConfigString()."));\n" .
            "})();");

        $_Value = join(",", (array) $this->getValue());

        return $this->getLabel() . "\n" .
            "<div class=\"".$this->getClassString()."\">\n" .
            "    <input type=\"hidden\" name=\"".$this->getField()."\" id=\"".$this->getId()."_input\" lay-filter=\"".$this->getId()."_input\" value=\"".$_Value."\" />\n" .
            "    <div id=\"".$this->getId()."\" lay-filter=\"".$this->getId()."\" ".$this->getAttrString()." ></div>\n" .
            "</div>";
    }
}