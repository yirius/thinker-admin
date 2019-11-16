<?php


namespace Yirius\Admin\layout;


use Yirius\Admin\extend\ThinkerLayout;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class ThinkerPage
 * @package Yirius\Admin\layout
 * @method ThinkerPage setTitle($title);
 * @method string getTitle();
 */
class ThinkerPage extends ThinkerLayout
{
    /**
     * PageView Title
     * use for <title></title>
     *
     * @var string
     */
    protected $title = "界面";

    /**
     * class which extend Layout
     * @var array<ThinkerLayout>
     */
    protected $layouts = [];

    /**
     * @var null
     */
    protected $breadcrumb = null;

    /**
     * ThinkerPage constructor.
     * @param callable|null $callable
     */
    public function __construct(callable $callable = null)
    {
        parent::__construct();

        if (is_callable($callable)) {
            call($callable, [$this]);
        }
    }

    /**
     * @title       layout
     * @description 设置界面的展示效果
     * @createtime  2019/11/15 12:00 下午
     * @param ThinkerLayout $layout
     * @return $this
     * @author      yangyuance
     */
    public function layout(ThinkerLayout $layout)
    {
        $this->layouts[] = $layout;

        return $this;
    }

    /**
     * @title      card
     * @description
     * @createtime 2019/11/15 3:30 下午
     * @param callable|null $callable
     * @return ThinkerCard
     * @author     yangyuance
     */
    public function card(callable $callable = null)
    {
        $layout = (new ThinkerCard($callable));

        $this->layouts[] = $layout;

        return $layout;
    }

    /**
     * @title      rows
     * @description
     * @createtime 2019/11/16 6:13 下午
     * @param callable|null $callable
     * @return ThinkerRows
     * @author     yangyuance
     */
    public function rows(callable $callable = null)
    {
        $layout = (new ThinkerRows($callable));

        $this->layouts[] = $layout;

        return $layout;
    }

    /**
     * @title      collapse
     * @description
     * @createtime 2019/11/16 6:16 下午
     * @param callable|null $callable
     * @return ThinkerCollapse
     * @author     yangyuance
     */
    public function collapse(callable $callable = null)
    {
        $layout = (new ThinkerCollapse($callable));

        $this->layouts[] = $layout;

        return $layout;
    }

    /**
     * @title      breadcrumb
     * @description
     * @createtime 2019/11/16 6:15 下午
     * @param callable|null $callable
     * @return ThinkerBreadcrumb|null
     * @author     yangyuance
     */
    public function breadcrumb(callable $callable = null)
    {
        if(is_null($this->breadcrumb)){
            $this->breadcrumb = (new ThinkerBreadcrumb($callable));
        }

        return $this->breadcrumb;
    }

    /**
     * @title       render
     * @description 每一个组件需要继承渲染接口
     * @createtime  2019/11/14 4:26 下午
     * @return string
     * @author      yangyuance
     */
    public function render()
    {
        //所有的html内容渲染
        $layouts = join("\n", array_map(function(ThinkerLayout $value){
            return $value->render();
        }, $this->layouts));

        //判断所有的js
        $runScript = ThinkerAdmin::getScript();
        //组装使用的模板
        $templates = join("\n", $runScript['template']);
        //layui使用的js
        $useFiles = json_encode($runScript['use']);
        //运行的js
        $useScript = join("\n", $runScript['script']);
        //使用的src
        $srcScript = join("\n", array_map(function($value){
            return "layui.link(layui.cache.base".$value.".css?v='+layui.conf.v);";
        }, $runScript['file']));

        //渲染面包屑
        $breadcrumb = "";
        if(!is_null($this->breadcrumb)){
            $breadcrumb = $this->breadcrumb->render();
        }

        return <<<HTML
{$breadcrumb}
<div class="layui-fluid {$this->getClass()}" id="{$this->getId()}" lay-title="{$this->title}" {$this->getAttrs()}>
{$layouts}
{$templates}
</div>
<script>
{$srcScript}
layui.use({$useFiles}, function(){
    function load(){
        var $ = layui.jquery;
        {$useScript}
    }
    if(!layui.common){
        layui.use('common', load);
    }else{
        layui.cache.callback.common();
        load();
    }
});
</script>
HTML
            ;
    }
}