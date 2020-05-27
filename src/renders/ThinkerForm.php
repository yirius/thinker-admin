<?php


namespace Yirius\Admin\renders;


use Yirius\Admin\renders\form\ThinkerAssemblys;
use Yirius\Admin\renders\form\ThinkerInline;
use Yirius\Admin\renders\form\ThinkerTab;
use Yirius\Admin\support\abstracts\LayoutAbstract;
use Yirius\Admin\templates\TemplateList;
use Yirius\Admin\ThinkerAdmin;

class ThinkerForm extends ThinkerAssemblys
{
    public function __construct(callable $closure = null)
    {
        parent::__construct();

        if(is_callable($closure)) call($closure, [$this]);
    }

    protected $footerTrigger = false;
    protected $footerHtml = "";
    protected $tabs = [];

    /**
     * @title      footer
     * @description
     * @createtime 2020/5/27 4:42 下午
     * @param $html
     * @return $this
     * @author     yangyuance
     */
    public function footer($html) {
        $this->footerTrigger = true;
        $this->footerHtml = $html;
        return $this;
    }

    public function submit($url, $id = null, $successCall = null, $beforeSubmit = null) {
        $doneCall = "layui.admin.reloadCurrentTable();layui.layer.close(layui.layer.index);layui.layer.msg(msg);" . (empty($successCall) ? "": $successCall);
        $beforeSubmit = empty($beforeSubmit) ? "" : str_replace(["\n", "\r", "'"], "", $beforeSubmit);

        $requestMethod = "post";

        if(!is_null($id)) {
            $requestMethod = "put";

            if($id != 0){
                if(strpos($url, "?") !== false){
                    $viewUrl = explode("?", $url);
                    $url = $viewUrl[0] . "/" . $id . "?" . $viewUrl[1];
                }else{
                    $url = $url . "/" . $id;
                }
            }
        }

        ThinkerAdmin::script(TemplateList::form()->SubmitJs()->templates([
            $this->getId()."-submit",
            $url,
            $requestMethod,
            $beforeSubmit,
            $doneCall
        ])->render());

        return $this;
    }

    /**
     * @title      inline
     * @description
     * @createtime 2020/5/27 4:46 下午
     * @param callable|null $closure
     * @return ThinkerInline
     * @author     yangyuance
     */
    public function inline(callable $closure = null) {
        $inline = new ThinkerInline($closure, $this->getUseValue());
        $this->addAssemblys($inline);
        return $inline;
    }

    /**
     * @title      tab
     * @description
     * @createtime 2020/5/27 4:47 下午
     * @param               $tabName
     * @param callable|null $closure
     * @return ThinkerTab
     * @author     yangyuance
     */
    public function tab($tabName, callable $closure = null) {
        $tab = new ThinkerTab($closure, $this->getUseValue());
        $tab->setTitle($tabName);
        $this->tabs[] = $tab;
        return $tab;
    }

    /**
     * @title parseTabs
     * @description
     * @createtime 2019/3/12 下午11:56
     * @return string
     */
    protected function parseTabs()
    {
        if(empty($this->tabs)){
            return '';
        }else{
            //judge tabs and make string
            $tabsHtml = ['title' => [], 'content' => []];
            foreach($this->tabs as $i => $v){
                $tabsHtml['title'][] = "<li class='".($i==0?'layui-this':'')."'>" . $v->getTitle() . "</li>";
                $tabsHtml['content'][] = '<div class="layui-tab-item '.($i==0?'layui-show':'').'">' . $v->render() . '</div>';
            }

            return '<div class="layui-tab"><ul class="layui-tab-title">'.join("", $tabsHtml['title']).'</ul><div class="layui-tab-content">'.join("", $tabsHtml['content']).'</div>';
        }
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
        if(!$this->footerTrigger){
            $this->footer('<button class="layui-btn" lay-submit="" lay-filter="'.$this->getId().'-submit">立即提交</button>');
        }

        //所有组件的渲染
        $assemblys = join("\n", array_map(function(LayoutAbstract $value){
            return '<div class="layui-form-item">' . ($value->render()) . "</div>";
        }, $this->assemblys));

        $footer = '';
        if(!empty($this->footerHtml)){
            $footer = '<div class="layui-form-item">
    <div class="layui-input-block">
        <div class="layui-footer">
            '.$this->footerHtml.'
        </div>
    </div>
</div>';
        }

        //return all string
        return <<<HTML
<form class="layui-form" lay-filter="{$this->getId()}" id="{$this->getId()}">
{$this->parseTabs()}
{$assemblys}
{$footer}
</form>
HTML
            ;
    }

    /**
     * @title      send
     * @description
     * @createtime 2020/5/27 5:27 下午
     * @param string        $title
     * @param callable|null $callable
     * @return string|void
     * @author     yangyuance
     */
    public function send($title = "添加", callable $callable = null) {
        if(is_callable($callable)) {
            return (new ThinkerPage(function(ThinkerPage $thinkerPage) use($callable){
                $thinkerPage->setFormOrTable($this);
                call($callable, [$thinkerPage]);
            }))->setTitle($title)->render();
        } else {
            return (new ThinkerPage(function(ThinkerPage $thinkerPage) use($callable){
                $thinkerPage->card()->addBodyLayout($this)
                    ->addCardHeaderClass("padding-tb-sm");
            }))->setTitle($title)->render();
        }
    }
}