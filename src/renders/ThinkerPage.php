<?php


namespace Yirius\Admin\renders;


use Yirius\Admin\renders\page\ThinkerBreadcrumb;
use Yirius\Admin\renders\page\ThinkerCard;
use Yirius\Admin\renders\page\ThinkerCollapse;
use Yirius\Admin\renders\page\ThinkerRows;
use Yirius\Admin\support\abstracts\LayoutAbstract;
use Yirius\Admin\ThinkerAdmin;

class ThinkerPage extends LayoutAbstract
{
    public function __construct(callable $callable = null)
    {
        parent::__construct();

        if(is_callable($callable)) call($callable, [$this]);
    }

    private $title = "界面";

    /**
     * @title      setTitle
     * @description
     * @createtime 2020/5/27 5:23 下午
     * @param $title
     * @return $this
     * @author     yangyuance
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    private $formOrTable = null;

    /**
     * @title      setFormOrTable
     * @description
     * @createtime 2020/5/27 5:23 下午
     * @param $formOrTable
     * @return $this
     * @author     yangyuance
     */
    public function setFormOrTable(LayoutAbstract $formOrTable)
    {
        $this->formOrTable = $formOrTable;
        return $this;
    }

    /**
     * @return LayoutAbstract
     */
    public function getFormOrTable()
    {
        return $this->formOrTable;
    }

    private $layouts = [];

    private $thinkerBreadcrumb = null;


    /**
     * @title      layout
     * @description
     * @createtime 2020/5/27 5:17 下午
     * @param LayoutAbstract $layout
     * @return $this
     * @author     yangyuance
     */
    public function layout(LayoutAbstract $layout) {
        $this->layouts[] = $layout;
        return $this;
    }

    /**
     * @title      card
     * @description
     * @createtime 2020/5/27 5:17 下午
     * @param callable|null $closure
     * @return ThinkerCard
     * @author     yangyuance
     */
    public function card(callable $closure = null) {
        $card = new ThinkerCard($closure);
        $this->layouts[] = $card;
        return $card;
    }

    /**
     * @title      rows
     * @description
     * @createtime 2020/5/27 5:17 下午
     * @param callable|null $closure
     * @return ThinkerRows
     * @author     yangyuance
     */
    public function rows(callable $closure = null) {
        $rows = new ThinkerRows($closure);
        $this->layouts[] = $rows;
        return $rows;
    }

    /**
     * @title      collapse
     * @description
     * @createtime 2020/5/27 5:17 下午
     * @param callable|null $closure
     * @return ThinkerCollapse
     * @author     yangyuance
     */
    public function collapse(callable $closure = null) {
        $collapse = new ThinkerCollapse($closure);
        $this->layouts[] = $collapse;
        return $collapse;
    }

    /**
     * @title      breadcrumb
     * @description
     * @createtime 2020/5/27 5:17 下午
     * @param callable|null $closure
     * @return ThinkerBreadcrumb|null
     * @author     yangyuance
     */
    public function breadcrumb(callable $closure = null) {
        if($this->thinkerBreadcrumb == null) {
            $this->thinkerBreadcrumb = new ThinkerBreadcrumb($closure);
        }
        return $this->thinkerBreadcrumb;
    }

    /**
     * @title      render
     * @description
     * @createtime 2020/5/27 5:17 下午
     * @return string|void
     * @author     yangyuance
     */
    public function render()
    {
        $layouts = join("\n", array_map(function (LayoutAbstract $layoutAbstract) {
            return $layoutAbstract->render();
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
            return "layui.link(layui.cache.base.'".$value.".js?v='.layui.conf.v);";
        }, $runScript['file']));

        //渲染面包屑
        $breadcrumb = "";
        if(!is_null($this->thinkerBreadcrumb)){
            $breadcrumb = $this->thinkerBreadcrumb->render();
        }

        //判断所有的css
        $runStyle = ThinkerAdmin::getStyle();
        //得到需要引入的，然后渲染
        $cssFiles = join("\n", array_map(function($value){
            return "layui.link(layui.cache.base.'css/".$value.".css?v='.layui.conf.v);";
        }, $runStyle['file']));

        return <<<HTML
{$breadcrumb}
<div class="layui-fluid {$this->getClassString()}" id="{$this->getId()}_page" lay-title="{$this->title}" {$this->getAttrString()}>
{$layouts}
{$templates}
</div>
<script>
{$cssFiles}
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