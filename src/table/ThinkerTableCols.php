<?php


namespace Yirius\Admin\table;


use Yirius\Admin\extend\ThinkerLayout;
use Yirius\Admin\form\assemblys\Button;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class ThinkerColumn
 * @method ThinkerTableCols setField($value)
 * @method ThinkerTableCols setTitle($value)
 * @method ThinkerTableCols setWidth($value)
 * @method ThinkerTableCols setMinWidth(int $value)
 * @method ThinkerTableCols setType($value) 存在normal/checkbox/radio/numbers/space几种类型
* // * @method ThinkerColumn setAllChecked(bool $checked)
 * @method ThinkerTableCols setFixed($value) 有left和right两个值
 * @method ThinkerTableCols setHide(bool $isShow) 是否隐藏，初始不隐藏
 * @method ThinkerTableCols setTotalRow(bool $value) 是否开启列合计
 * @method ThinkerTableCols setTotalRowText($value) 列合计的名称
 * @method ThinkerTableCols setSort(bool $value) 可以筛选
 * @method ThinkerTableCols setUnresize(bool $value)
 * @method ThinkerTableCols setEdit($value) text或者select
 * @method ThinkerTableCols setEvent($value)
 * @method ThinkerTableCols setStyle($value)
 * @method ThinkerTableCols setAlign($value)
 * @method ThinkerTableCols setColspan(int $value)
 * @method ThinkerTableCols setRowspan(int $value)
 * @method ThinkerTableCols setTemplet($value)
 * @method ThinkerTableCols setToolbar($value)
 *
 * @method ThinkerTableCols getField()
 * @method ThinkerTableCols getTitle()
 * @method ThinkerTableCols getWidth()
 * @method ThinkerTableCols getMinWidth()
 * @method ThinkerTableCols getType()
 * @method ThinkerTableCols getAllChecked()
 * @method ThinkerTableCols getFixed()
 * @method ThinkerTableCols getHide()
 * @method ThinkerTableCols getTotalRow()
 * @method ThinkerTableCols getTotalRowText()
 * @method ThinkerTableCols getSort()
 * @method ThinkerTableCols getUnresize()
 * @method ThinkerTableCols getEdit()
 * @method ThinkerTableCols getEvent()
 * @method ThinkerTableCols getStyle()
 * @method ThinkerTableCols getAlign()
 * @method ThinkerTableCols getColspan()
 * @method ThinkerTableCols getRowspan()
 * @method ThinkerTableCols getTemplet()
 * @method ThinkerTableCols getToolbar()
 * @package Yirius\Admin\table
 */
class ThinkerTableCols extends ThinkerLayout
{
    /**
     * @var array
     */
    protected $config = [
        'field' => '',
        'title' => ''
    ];

    /**
     * @var ThinkerTable|null
     */
    protected $tableIns = null;

    /**
     * 当前使用的tool的html
     * @var string
     */
    protected $toolHtml = '';

    /**
     * ThinkerColumn constructor.
     * @param                   $field
     * @param                   $title
     * @param ThinkerTable|null $table
     */
    public function __construct($field, $title, ThinkerTable $table = null)
    {
        parent::__construct();

        $this->setField($field);

        $this->setTitle($title);

        $this->tableIns = $table;
    }

    /**
     * @title      toolbar
     * @description 设置cols的toolbar
     * @createtime 2019/11/14 11:56 下午
     * @param $html
     * @return $this
     * @author     yangyuance
     */
    public function toolbar($html)
    {
        $this->toolHtml .= $html;

        return $this;
    }

    /**
     * @title      button
     * @description
     * @createtime 2019/11/14 6:40 下午
     * @param       $text
     * @param       $event
     * @param       $icon
     * @param       $class
     * @param bool  $isHref
     * @param array $attrs
     * @return ThinkerTableCols
     * @author     yangyuance
     */
    public function button($text, $event, $icon, $class, $isHref = false, $attrs = [])
    {
        return $this->toolbar(
            (new Button())->xs()
                ->setText('<i class="layui-icon layui-icon-'. $icon .'"></i>'.$text)
                ->setClass($class)
                ->setAttrs(($isHref ? 'thinker-href' : 'lay-event') . '="'.$event.'"')
                ->setAttrs($attrs)
                ->render()
        );
    }

    /**
     * @title      edit
     * @description
     * @createtime 2019/11/14 6:41 下午
     * @param string $text
     * @param string $icon
     * @return ThinkerTableCols
     * @author     yangyuance
     */
    public function edit($text = "编辑", $icon = "edit")
    {
        return $this->button($text, 'edit', $icon, 'layui-btn-normal');
    }

    /**
     * @title      delete
     * @description
     * @createtime 2019/11/14 6:57 下午
     * @param string $text
     * @param string $icon
     * @return ThinkerTableCols
     * @author     yangyuance
     */
    public function delete($text = "删除", $icon = "delete")
    {
        return $this->button($text, 'delete', $icon, 'layui-btn-danger');
    }

    /**
     * @title      expend
     * @description
     * @createtime 2019/11/14 6:56 下午
     * @return mixed
     * @author     yangyuance
     */
    public function expend()
    {
        $this->setWidth(55);

        return $this->toolbar((new Button())->xs()->setAttrs('lay-event="expend"')->setAttrs('style="width: 20px;height: 20px;border-radius: 10px;line-height: 20px;cursor: pointer;padding: 0 0 0 3px;"')->setText('<i class="layui-icon layui-icon-add-1"></i>')->render());
    }

    /**
     * @title      switchs
     * @description 进行开关选择
     * @createtime 2019/11/15 12:07 上午
     * @param        $field
     * @param null   $url
     * @param array  $config
     * @param string $beforePut
     * @param string $afterPut
     * @return $this
     * @author     yangyuance
     */
    public function switchs($field, $url = null, $config = [], $beforePut = '', $afterPut = '')
    {
        $prefixName = $field;

        if(!is_null($this->tableIns)){
            $prefixName = $this->tableIns->getId();

            if(is_null($url)){
                if(strpos($this->tableIns->getRestfulUrl(), "?") != false){
                    $restfulUrl = explode("?", $this->tableIns->getRestfulUrl());
                    $url = $restfulUrl[0] . "/{{d.id}}?__type=field&" . $restfulUrl[1];
                }else{
                    $url = $this->tableIns->getRestfulUrl() . "/{{d.id}}?__type=field";
                }
            }
        }

        $config = array_merge([
            'checkedValue' => 1,
            'unCheckedValue' => 0,
            'filter' => $field,
            'field' => $field
        ], $config);

        /**
         * 设置点击之后的触发效果
         */
        ThinkerAdmin::script(<<<HTML
layui.form.on("switch(switch{$config['filter']})", function(obj){
    var renderData = JSON.parse(obj.elem.dataset.json), beforePut = '{$beforePut}', afterPut = '{$afterPut}';
    if(beforePut) renderData = (new Function('return'+beforePut))()(renderData);
    layui.admin.http.put(layui.laytpl("{$url}").render(renderData), {value: obj.elem.checked ? {$config['checkedValue']} : {$config['unCheckedValue']}, field: "{$config['filter']}"}, function(code,msg,data,all){if(afterPut) (new Function('return'+afterPut))()(code,msg,data,all);});
});
HTML
        );

        $this->setTemplet("{$prefixName}_{$field}_templet");

        //设置模板
        ThinkerAdmin::script(<<<HTML
<script type="text/html" id="{$prefixName}_{$field}_templet">
<input type="checkbox" name="{$config['filter']}" value="{$config['checkedValue']}" data-json="{{=JSON.stringify(d) }}" lay-skin="switch" lay-text="开|关" lay-filter="switch{$config['filter']}" {{ d.{$config['field']} == {$config['checkedValue']} ? 'checked' : '' }}>
</script>
HTML
        , false, false, true);

        return $this;
    }

    /**
     * @title      render
     * @description
     * @createtime 2019/11/14 6:44 下午
     * @return array|string
     * @author     yangyuance
     */
    public function render()
    {
        $config = $this->config;

        if(empty($this->toolHtml)){
            unset($config['toolbar']);
        }else{
            $config['toolbar'] = "<div>".$this->toolHtml."</div>";
        }

        return $config;
    }
}