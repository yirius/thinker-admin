<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/26
 * Time: 下午3:49
 */

namespace Yirius\Admin\table;


use Yirius\Admin\Admin;
use Yirius\Admin\Layout;


/**
 * Class Columns
 * @method Columns setField($value)
 * @method Columns setTitle($value)
 * @method Columns setWidth($value)
 * @method Columns setMinWidth($value)
 * @method Columns setType($value)
 * @method Columns setAllChecked($checked = true)
 * @method Columns setFixed($value = 'right')
 * @method Columns setHide($isShow = false)
 * @method Columns setTotalRow($value = true)
 * @method Columns setTotalRowText($value = '合计')
 * @method Columns setSort($value = false)
 * @method Columns setUnresize($value = false)
 * @method Columns setEdit($value = 'text')
 * @method Columns setEvent($value)
 * @method Columns setStyle($value)
 * @method Columns setAlign($value = "left")
 * @method Columns setColspan($value = 1)
 * @method Columns setRowspan($value = 1)
 * @method Columns setTemplet($value)
 * @method Columns setToolbar($value)
 * @method Columns getField()
 * @method Columns getTitle()
 * @method Columns getWidth()
 * @method Columns getMinWidth()
 * @method Columns getType()
 * @method Columns getAllChecked()
 * @method Columns getFixed()
 * @method Columns getHide()
 * @method Columns getTotalRow()
 * @method Columns getTotalRowText()
 * @method Columns getSort()
 * @method Columns getUnresize()
 * @method Columns getEdit()
 * @method Columns getEvent()
 * @method Columns getStyle()
 * @method Columns getAlign()
 * @method Columns getColspan()
 * @method Columns getRowspan()
 * @method Columns getTemplet()
 * @method Columns getToolbar()
 * @package Yirius\Admin\table
 */
class Columns extends Layout
{
    /**
     * @var Table
     */
    protected $table = null;

    /**
     * @var array
     */
    protected $config = [
        'field' => '',
        'title' => ''
    ];

    /**
     * @var string
     */
    protected $tool = '';

    /**
     * @var array
     */
    protected $fullTemplet = [];

    /**
     * Columns constructor.
     * @param $field
     * @param $title
     * @param $table
     */
    public function __construct($field, $title, $table)
    {
        $this->table = $table;

        $this->config['field'] = $field;

        $this->config['title'] = $title;
    }

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        $config = $this->config;

        if(empty($this->tool)){
            unset($config['toolbar']);
        }else{
            $config['toolbar'] = "#" . $config['toolbar'];
        }

        return $config;
    }

    /**
     * @return string
     */
    public function getTool()
    {
        $fullTempletString = '';
        foreach($this->fullTemplet as $i => $v){
            $fullTempletString .= <<<HTML
<script type="text/html" id="{$i}">
{$v}
</script>
HTML;
        }


        $toolString = empty($this->tool) ? '' : <<<HTML
<script type="text/html" id="{$this->getToolbar()}">
{$this->tool}
</script>
HTML;

        return $fullTempletString . $toolString;
    }

    /**
     * @title tool
     * @description
     * @createtime 2019/2/26 下午5:36
     * @param $html
     * @return $this
     */
    public function tool($html)
    {
        $this->tool .= $html;

        return $this;
    }

    /**
     * @title button
     * @description
     * @createtime 2019/3/7 下午9:20
     * @param $text
     * @param $event
     * @param $icon
     * @param $class
     * @param bool $isHref
     * @param string $attrs
     * @return Columns
     */
    public function button($text, $event, $icon, $class, $isHref = false, $attrs = '')
    {
        $this->setWidth(150);

        $attr = $isHref ? "thinker-href" : "lay-event";

        return $this->tool('<a class="layui-btn layui-btn-xs '. $class .'" '.$attrs . ' '. $attr.'="'. $event .'"><i class="layui-icon layui-icon-'. $icon .'"></i>'. $text .'</a>');
    }

    /**
     * @title edit
     * @description
     * @createtime 2019/2/26 下午5:39
     * @return Columns
     */
    public function edit()
    {
        return $this->button('编辑', 'edit', 'edit', 'layui-btn-normal');
    }

    /**
     * @title edit
     * @description
     * @createtime 2019/2/26 下午5:39
     * @return Columns
     */
    public function delete()
    {
        return $this->button('删除', 'delete', 'delete', 'layui-btn-danger');
    }

    /**
     * @title expend
     * @description expend this row
     * @createtime 2019/2/27 下午4:11
     */
    public function expend()
    {
        $this->setWidth(55);

        return $this->tool('<a class="layui-btn layui-btn-xs" lay-event="expend" style="width: 20px;height: 20px;border-radius: 10px;line-height: 20px;cursor: pointer;padding: 0 0 0 3px;"><i class="layui-icon layui-icon-add-1"></i></a>');
    }

    /**
     * @title setFullTemplet
     * @description
     * @createtime 2019/2/28 下午2:54
     * @param $id
     * @param $fullTemplet
     * @return $this
     */
    public function setFullTemplet($id, $fullTemplet)
    {
        $this->fullTemplet[$id] = $fullTemplet;

        return $this;
    }

    /**
     * @title setSwitchTemplet
     * @description
     * @createtime 2019/3/1 下午2:39
     * @param $field
     * @param null $url
     * @param array $config
     * @return Columns
     */
    public function setSwitchTemplet($field, $url = null, $config = [])
    {
        if(is_null($url)) $url = $this->table->getRestfulUrl() . "/{{d.id}}?__type=field";

        $config = array_merge([
            'checkedValue' => 1,
            'unCheckedValue' => 0,
            'filter' => $field,
            'field' => $field
        ], $config);

        Admin::script(<<<HTML
layui.form.on("switch(switch{$config['filter']})", function(obj){
    var renderData = JSON.parse(obj.elem.dataset.json);
    layui.http.put(layui.laytpl("{$url}").render(renderData), {value: obj.elem.checked ? {$config['checkedValue']} : {$config['unCheckedValue']}, field: "{$config['filter']}"});
});
HTML
        );

        $this->setTemplet('#' . $this->table->getName() . "_" . $field . "_templet");

        return $this->setFullTemplet($this->table->getName() . "_" . $field . "_templet", <<<HTML
<input type="checkbox" name="{$config['filter']}" value="{$config['checkedValue']}" data-json="{{=JSON.stringify(d) }}" lay-skin="switch" lay-text="开|关" lay-filter="switch{$config['filter']}" {{ d.{$config['field']} == {$config['checkedValue']} ? 'checked' : '' }}>
HTML
        );
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