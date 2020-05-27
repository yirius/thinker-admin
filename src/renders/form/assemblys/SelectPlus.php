<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class SelectPlus
 * @package Yirius\Admin\renders\form\assemblys
 * @method SelectPlus setContent($html);
 * @method SelectPlus setTips($tips);
 * @method SelectPlus setEmpty($empty);
 * @method SelectPlus setFilterable(bool $search);
 * @method SelectPlus setSearchTips($tips);
 * @method SelectPlus setDelay(int $delay);
 * @method SelectPlus setRemoteSearch(bool $search);
 * @method SelectPlus setDirection($direction); 	auto / up / down
 * @method SelectPlus setStyle(array $style);
 * @method SelectPlus setHeight(int $height);
 * @method SelectPlus setPaging(bool $paging);
 * @method SelectPlus setPageSize(int $pageSize);
 * @method SelectPlus setPageEmptyShow(bool $PageEmptyShow);
 * @method SelectPlus setPageRemote(bool $Remote);
 * @method SelectPlus setRadio(bool $radio);
 * @method SelectPlus setRepeat(bool $repeat);
 * @method SelectPlus setClickClose(bool $clickClose);
 * @method SelectPlus setMax(int $max);
 * @method SelectPlus setShowCount(int $showCount);
 * @method SelectPlus setSize($size); large / medium / small / mini
 * @method SelectPlus setDisabled(bool $disabled);
 */
class SelectPlus extends Assembly
{
    public $configsFields = [
        "content", "tips", "empty", "filterable", "searchTips", "delay",
        "remoteSearch", "direction", "style", "height", "pageing", "pageSize",
        "pageEmptyShow", "pageRemote", "radio", "repeat", "clickClose", "max",
        "showCount", "size", "disabled"
    ];

    protected $data = [];

    /**
     * @title      setData
     * @description
     * @createtime 2020/5/27 3:53 下午
     * @param array $data
     * @return $this
     * @author     yangyuance
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        ThinkerAdmin::script("xmSelect", false, true);
    }


    /**
     * @title options
     * @description 设置值
     * @author YangYuanCe
     * @param textValues
     * @return {@link SelectPlus}
     **/
    public function options($textValues) {
        if($textValues != null) {
            $this->setData($textValues);
        }
        return $this;
    }

    public function radio($radio = true)
    {
        return $this->setRadio($radio);
    }

    public function search()
    {
        return $this->setFilterable(true);
    }

    /** 渲染各种回调 **/

    /** 过滤回调 **/
    protected $filterMethod = "";

    public function setFilterMethod($callback)
    {
        $this->filterMethod = "filterMethod: function(val, item, index, prop){\n" .
            $callback .
            "return false;\n" .
            "},\n";

        return $this;
    }

    /** 过滤完成回调 **/
    protected $filterDone = "";

    public function setFilterDone($callback)
    {
        $this->filterDone = "filterDone: function(val, list){\n" .
            $callback .
            "},\n";

        return $this;
    }

    /** 远程搜索方法 **/
    protected $remoteMethod = "";

    public function setRemoteMethod($callback)
    {
        $this->remoteMethod = "remoteMethod: function(val, cb, show, pageIndex){\n" .
            "if(!val){ return cb([]); }" .
            $callback .
            "},\n";

        return $this;
    }

    /** 打开隐藏的回调 **/
    protected $show = "";
    protected $hide = "";

    public function setShow($callback)
    {
        $this->show = "show: function(val, cb, show, pageIndex){\n" .
            $callback .
            "},\n";

        return $this;
    }

    public function setHide($callback)
    {
        $this->hide = "hide: function(val, cb, show, pageIndex){\n" .
            $callback .
            "},\n";

        return $this;
    }

    /** 自定义渲染选项 **/
    private $template = "";

    public function setTemplate($callback)
    {
        $this->template = "template: function(item, sels, name, value){\n" .
            $callback .
            "},\n";

        return $this;
    }

    /** 自定义渲染选项 **/
    private $onEvent = "";

    public function setOn($callback)
    {
        $this->onEvent = "on: function(data){\n" .
            $callback .
            "},\n";

        return $this;
    }

    /**
     * @title  设置model显示设置
     * @description
     * @param null
     * @return {@link null}
     **@author YangYuanCe
     */
    private $model = "";

    public function setModel($model)
    {
        $this->model = "model: " . $model . ",\n";

        return $this;
    }

    /**
     * @title  下拉显示树
     * @description
     * @param null
     * @return {@link null}
     **@author YangYuanCe
     */
    private $tree = "";

    public function setTree($tree)
    {
        $this->tree =
            "tree: " . $tree . ",\n";

        return $this;
    }

    /**
     * @title  下拉显示级联菜单
     * @description
     * @param null
     * @return {@link null}
     **@author YangYuanCe
     */
    private $cascader = "";

    public function setCascader($cascader)
    {
        $this->cascader = "cascader: " . $cascader . ",\n";

        return $this;
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
        //渲染value
        $value = $this->getValue();
        $_Value = "[]";
        if(!empty($value)) {
            $_Value = "[".$value."]";
        }

        $data = empty($this->data) ? "[]" : json_encode($this->data);

        ThinkerAdmin::script(";window.".$this->getId()." = layui.xmSelect.render($.extend({\n" .
            "    el: '#".$this->getId()."',\n" .
            "    language: 'zn',\n" .
            "    name: \"".$this->getField()."\",\n" .
            "    prop: {\n" .
            "        name: 'text',\n" .
            "        value: 'value',\n" .
            "        selected: 'checked',\n" .
            "        children: 'childs',\n" .
            "    },\n" .
            "    initValue: ".$_Value.",\n" .
            "    data: ".$data.",\n" .
            "    " . $this->filterMethod .
            "    " . $this->filterDone .
            "    " . $this->remoteMethod .
            "    " . $this->show .
            "    " . $this->hide .
            "    " . $this->template .
            "    " . $this->onEvent .
            "    " . $this->model .
            "    " . $this->tree .
            "    " . $this->cascader .
            "}, ".$this->getConfigString()."));");

        return $this->getLabel() . "\n" .
            "<div class=\"".$this->getClassString()."\">\n" .
            "    <div name=\"".$this->getField()."\" id=\"".$this->getId()."\" lay-filter=\"".$this->getId()."\" ".$this->getAttrString()."></div>\n" .
            "</div>";
    }
}