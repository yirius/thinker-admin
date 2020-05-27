<?php


namespace Yirius\Admin\renders\table;


use Yirius\Admin\renders\form\assemblys\Button;
use Yirius\Admin\renders\ThinkerTable;
use Yirius\Admin\support\abstracts\LayoutAbstract;
use Yirius\Admin\templates\TemplateList;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class ThinkerColumns
 * @package Yirius\Admin\renders\table
 *
 * @method ThinkerColumns setField(string $value)
 * @method ThinkerColumns setTitle(string $value)
 * @method ThinkerColumns setWidth($value)
 * @method ThinkerColumns setMinWidth(int $value)
 * @method ThinkerColumns setType(string $value) 存在normal/checkbox/radio/numbers/space几种类型
 * @method ThinkerColumns setAllChecked(bool $checked)
 * @method ThinkerColumns setFixed($value) 有left和right两个值
 * @method ThinkerColumns setHide(bool $isShow) 是否隐藏，初始不隐藏
 * @method ThinkerColumns setTotalRow(bool $value) 是否开启列合计
 * @method ThinkerColumns setTotalRowText($value) 列合计的名称
 * @method ThinkerColumns setSort(bool $value) 可以筛选
 * @method ThinkerColumns setUnresize(bool $value)
 * @method ThinkerColumns setEdit($value) text或者select
 * @method ThinkerColumns setEvent($value)
 * @method ThinkerColumns setStyle($value)
 * @method ThinkerColumns setAlign($value)
 * @method ThinkerColumns setColspan(int $value)
 * @method ThinkerColumns setRowspan(int $value)
 * @method ThinkerColumns setTemplet($value)
 * @method ThinkerColumns setToolbar($value)
 *          
 * @method ThinkerColumns getField()
 * @method ThinkerColumns getTitle()
 * @method ThinkerColumns getWidth()
 * @method ThinkerColumns getMinWidth()
 * @method ThinkerColumns getType()
 * @method ThinkerColumns getAllChecked()
 * @method ThinkerColumns getFixed()
 * @method ThinkerColumns getHide()
 * @method ThinkerColumns getTotalRow()
 * @method ThinkerColumns getTotalRowText()
 * @method ThinkerColumns getSort()
 * @method ThinkerColumns getUnresize()
 * @method ThinkerColumns getEdit()
 * @method ThinkerColumns getEvent()
 * @method ThinkerColumns getStyle()
 * @method ThinkerColumns getAlign()
 * @method ThinkerColumns getColspan()
 * @method ThinkerColumns getRowspan()
 * @method ThinkerColumns getTemplet()
 * @method ThinkerColumns getToolbar()
 */
class ThinkerColumns extends LayoutAbstract
{
    protected $configsFields = [
        "field","title","width","minWidth","type","allChecked",
        "fixed","hide","totalRow","totalRowText","sort","unresize",
        "edit","event","style","align","colspan","rowspan","templet","toolbar"
    ];

    private $thinkerTable;

    /**
     * 当前使用的tool的html
     * @var string
     */
    protected $toolHtml = '';

    public function __construct($field, $name, ThinkerTable $thinkerTable)
    {
        parent::__construct();

        $this->setField($field);
        $this->setTitle($name);

        $this->thinkerTable = $thinkerTable;
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
     * @createtime 2020/5/27 5:47 下午
     * @param       $text
     * @param       $event
     * @param       $icon
     * @param       $useClass
     * @param bool  $isHref
     * @param array $attrs
     * @param null  $ifTpl
     * @return ThinkerColumns
     * @author     yangyuance
     */
    public function button($text, $event, $icon, $useClass, $isHref = false, $attrs = [], $ifTpl = null)
    {
        $button = (new Button("", '<i class="layui-icon layui-icon-'. $icon .'"></i>'.$text));

        $button->setTrimId($this->getId() . "_cols_" . $event)
            ->addClass($useClass)->addAttr($isHref ? "lay-href" : "lay-event", $event);

        if(!empty($attrs)) {
            foreach ($attrs as $i => $attr) {
                $button->addAttr($i, $attr);
            }
        }

        return $this->toolbar(
            empty($ifTpl) ? $button->xs()->render() : '{{# '.$ifTpl.'{ }}'.$button->xs()->render().'{{# } }}'
        );
    }

    /**
     * @title      edit
     * @description
     * @createtime 2020/5/27 5:49 下午
     * @param string $text
     * @param string $icon
     * @return ThinkerColumns
     * @author     yangyuance
     */
    public function edit($text = "编辑", $icon = "edit")
    {
        return $this->button($text, 'edit', $icon, 'layui-btn-normal');
    }

    /**
     * @title      delete
     * @description
     * @createtime 2020/5/27 5:49 下午
     * @param string $text
     * @param string $icon
     * @return ThinkerColumns
     * @author     yangyuance
     */
    public function delete($text = "删除", $icon = "delete")
    {
        return $this->button($text, 'delete', $icon, 'layui-btn-danger');
    }

    /**
     * @title      expend
     * @description
     * @createtime 2020/5/27 5:51 下午
     * @return ThinkerColumns
     * @author     yangyuance
     */
    public function expend()
    {
        $this->setWidth(50)->setAlign("center");

        return $this->toolbar(
            (new Button("", '<i class="layui-icon layui-icon-add-1"></i>'))
                ->xs()->addAttr('lay-event', 'expend')
                ->addAttr("style", "width: 20px;height: 20px;border-radius: 10px;line-height: 20px;cursor: pointer;padding: 0 0 0 3px;")
                ->render()
        );
    }

    /**
     * @title      switchs
     * @description
     * @createtime 2020/5/27 5:57 下午
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
        $prefixName = trim($field);

        if(!is_null($this->thinkerTable)){
            $prefixName = $this->thinkerTable->getId();

            if(is_null($url)){
                if(strpos($this->thinkerTable->getRestfulUrl(), "?") != false){
                    $restfulUrl = explode("?", $this->thinkerTable->getRestfulUrl());
                    $url = $restfulUrl[0] . "/{{d.id}}?__type=field&" . $restfulUrl[1];
                }else{
                    $url = $this->thinkerTable->getRestfulUrl() . "/{{d.id}}?__type=field&";
                }
            }
        }

        $config = array_merge([
            'checkedValue' => 1,
            'unCheckedValue' => 0,
            'filter' => $field,
            'field' => $field
        ], $config);


        $beforePut = empty($beforePut) ? "" : str_replace(["\n", "\r", "'"], "", $beforePut);
        $afterPut = empty($afterPut) ? "" : str_replace(["\n", "\r", "'"], "", $afterPut);

        ThinkerAdmin::script(TemplateList::table()->columns()->SwitchsJs()->templates([
            $url, $config, $beforePut, $afterPut
        ])->render());

        $this->setTemplet("#{$prefixName}_{$field}_templet");

        ThinkerAdmin::script(TemplateList::table()->columns()->SwitchsTpl()->templates([
            $prefixName."_".$field."_templet", $config
        ])->render(), false, false, true);

        return $this;
    }

    /**
     * @title      badgeTemplet
     * @description
     * @createtime 2020/5/27 5:59 下午
     * @param      $color
     * @param      $field
     * @param null $jsStr
     * @return ThinkerColumns
     * @author     yangyuance
     */
    public function badgeTemplet($color, $field, $jsStr = null) {
        if(empty($jsStr)) {
            return $this->setTemplet(
                "<div><span class='layui-badge layui-bg-".$color."'>{{".$field."}}</span></div>"
            );
        } else {
            return $this->setTemplet(
                "<div>".$jsStr."<span class='layui-badge layui-bg-".$color."'>{{".$field."}}</span></div>"
            );
        }
    }

    /**
     * @title       render
     * @description 每一个组件需要继承渲染接口
     * @createtime  2020/5/27 1:58 下午
     * @return array
     * @author      yangyuance
     */
    public function render()
    {
        $config = $this->configs;

        if(empty($this->toolHtml)){
            unset($config['toolbar']);
        }else{
            if(!empty($this->thinkerTable)) {
                $config['templet'] = "#{$this->thinkerTable->getId()}_{$this->getField()}_templet";
                ThinkerAdmin::script(<<<HTML
<script type="text/html" id="{$this->thinkerTable->getId()}_{$this->getField()}_templet">
{$this->toolHtml}
</script>
HTML
                    , false, false, true);
            }
        }

        return $config;
    }

}