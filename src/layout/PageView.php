<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 下午1:18
 */

namespace Yirius\Admin\layout;


use Yirius\Admin\Admin;
use Yirius\Admin\Layout;

class PageView extends Layout
{
    /**
     * PageView Title
     * use for <title></title>
     *
     * @var string
     */
    protected $title = "";

    /**
     * Page's breadcrumb
     * @var null
     */
    protected $breadcrumb = null;

    /**
     * class which extend Layout
     * @var array<Layout>
     */
    protected $layouts = [];

    /**
     * PageView constructor.
     * @param \Closure|null $callback
     */
    public function __construct(\Closure $callback = null)
    {
        if ($callback instanceof \Closure) {
            call_user_func($callback, $this);
        }
    }

    /**
     * @title setTitle
     * @description use for set pageview top title
     * @createtime 2019/1/30 下午2:44
     * @param string $title
     * @return PageView
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @title breadcrumb
     * @description set page's breadcrumb
     * @createtime 2019/2/24 下午5:26
     * @param $breadcrumbs
     * @return $this
     * @throws \Exception
     */
    public function breadcrumb($breadcrumbs)
    {
        if(is_null($this->breadcrumb)){
            $this->breadcrumb = new Breadcrumb($breadcrumbs);
        }else{
            if(is_array($breadcrumbs)){
                $this->breadcrumb->setBreadcrumb($breadcrumbs);
            }
        }

        return $this;
    }

    /**
     * @title setBreadcrumb
     * @description
     * @createtime 2019/2/25 下午11:25
     * @param $breadcrumb
     * @return $this
     */
    public function setBreadcrumb($breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;

        return $this;
    }

    /**
     * @title rows
     * @description use Closure or string to create an row
     * @createtime 2019/1/30 下午3:54
     * @param \Closure|string $rows
     * @return PageView
     */
    public function rows($rows)
    {
        if ($rows instanceof \Closure) {
            $rowsView = new Rows();
            call_user_func($rows, $rowsView);
            $this->setLayout($rowsView);
        } else {
            $this->setLayout(new Rows($rows));
        }

        return $this;
    }

    /**
     * @title card
     * @description
     * @createtime 2019/2/24 下午4:56
     * @param $cards
     * @return $this
     */
    public function card($cards)
    {
        if ($cards instanceof \Closure) {
            $cardView = new Card();
            call($cards, [$cardView]);
            $this->setLayout($cardView);
        } else {
            $this->setLayout(new Card(null, $cards));
        }

        return $this;
    }

    /**
     * @title setRows
     * @description
     * @createtime 2019/1/30 下午3:44
     * @param Layout|string $layout
     * @return PageView
     */
    public function setLayout(Layout $layout)
    {
        $this->layouts[] = $layout;

        return $this;
    }

    /**
     * @title formatCss
     * @description get css and format it
     * @createtime 2019/2/24 下午5:34
     */
    protected function formatStyle(){
        $style = Admin::getStyle();
        if(!empty($style['css'])){
            return "<style>" . join("\r\n", $style['css']) . "</style>";
        }else{
            return '';
        }
    }

    /**
     * @title formatJavascript
     * @description get Javascript and format it
     * @createtime 2019/2/24 下午5:42
     * @return string
     */
    protected function formatScript(){
        $style = Admin::getStyle();
        $script = Admin::getScript();

        $javascript = [];
        if(!empty($script['file'])){
            foreach($script['file'] as $i => $v){
                $javascript[] = '<script src="'. $v .'" />';
            }
        }
        $javascript = join("\r\n", $javascript);

        //prevent for load multi css files
        $css = [];
        if(!empty($style['file'])){
            foreach($style['file'] as $i => $v){
                $css[] = "layui.link(layui.cache.base + '../lib/css/".$v.".css?v=' + layui.thinkeradmin.v);";
            }
        }
        $cssFiles = join("", $css);

        $scriptString = join("\r\n", $script['script']);

        $useFiles = json_encode($script['use']);
        return <<<HTML
{$javascript}
<script>
    {$cssFiles}
    layui.use({$useFiles}, function(){
        function load(){
            var $ = layui.jquery;
            {$scriptString}
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

    /**
     * @title render
     * @description use for render each type
     * @createtime 2019/1/30 下午3:10
     * @return mixed
     */
    public function render()
    {
        $render = "";
        foreach ($this->layouts as $layout) {
            $render .= $layout->render();
        }

        $breadcrumb = "";
        if(!is_null($this->breadcrumb)){
            $breadcrumb = $this->breadcrumb->render();
        }

        if(config('thinkeradmin.isIframe')){

        }else{
            return response(<<<HTML
<title>{$this->title}</title>
{$this->formatStyle()}
{$breadcrumb}
<div class="layui-fluid">
    {$render}
</div>
{$this->formatScript()}
HTML
            );
        }
    }
}