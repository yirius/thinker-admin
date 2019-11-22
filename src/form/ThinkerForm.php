<?php


namespace Yirius\Admin\form;


use Yirius\Admin\extend\ThinkerLayout;
use Yirius\Admin\layout\ThinkerPage;
use Yirius\Admin\ThinkerAdmin;

class ThinkerForm extends ThinkerLayout
{
    use setExtend;

    /**
     * @var bool
     */
    protected $footerTrigger = false;

    /**
     * @var string
     */
    protected $footerHtml = '';

    /**
     * Form tab's content
     * @var array|ThinkerTab
     */
    protected $tabs = [];

    /**
     * @title      footer
     * @description
     * @createtime 2019/11/16 11:40 下午
     * @param $footerHtml
     * @return $this
     * @author     yangyuance
     */
    public function footer($footerHtml)
    {
        $this->footerTrigger = true;

        $this->footerHtml = $footerHtml;

        return $this;
    }

    /**
     * @title      submitEvent
     * @description
     * @createtime 2019/11/16 11:42 下午
     * @param      $url
     * @param int  $id
     * @param null $successCall
     * @param null $beforeSubmit
     * @return $this
     * @author     yangyuance
     */
    public function submit($url, $id = 0, $successCall = null, $beforeSubmit = null)
    {
        $doneCall = is_null($successCall) ? 'layui.admin.reloadTable();layui.layer.closeAll();layui.layer.msg(msg);' : $successCall;

        $beforeEvent = is_null($beforeSubmit) ? '' : htmlspecialchars($beforeSubmit);

        //judge restful url
        $requestMethod = 'post';

        if($id != 0){
            $requestMethod = "put";
            if(strpos($url, "?") != false){
                $viewUrl = explode("?", $url);
                $url = $viewUrl[0] . "/" . $id . "?" . $viewUrl[1];
            }else{
                $url = $url . "/" . $id;
            }
        }

        ThinkerAdmin::script(<<<HTML
layui.form.on("submit({$this->getId()}-submit)", function (obj) {
    //找到switch，然后给其赋值
    var switchs = $(obj.form).find('input[lay-skin="switch"]');
    layui.each(switchs, function(n,v){
        if(!v.checked){
            obj.field[v.name] = v.dataset.notuse;
        }
    });
    
    try{
        var beforeEvent = '{$beforeEvent}';
        if(beforeEvent){
            beforeEvent = new Function('return function(obj){' + beforeEvent + "}")();
            obj = beforeEvent(obj);
        }
        var url = layui.laytpl("{$url}").render(obj.field);
        layui.admin.http.{$requestMethod}(url, obj.field, function(code, msg, data, all){
            {$doneCall}
        });
    }catch(e){
        console.error(e);
    }
    return false;
});
HTML
        );

        return $this;
    }

    /**
     * @title      inline
     * @description
     * @createtime 2019/11/16 11:35 下午
     * @param callable $callable
     * @return ThinkerInline
     * @author     yangyuance
     */
    public function inline(callable $callable = null)
    {
        $inline = (new ThinkerInline($callable, $this->getValue()));

        $this->assemblys[] = $inline;

        return $inline;
    }

    /**
     * @title      tab
     * @description
     * @createtime 2019/11/16 11:49 下午
     * @param               $tabName
     * @param callable|null $callable
     * @return ThinkerTab
     * @author     yangyuance
     */
    public function tab($tabName, callable $callable = null)
    {
        $tab = (new ThinkerTab($callable, $this->getValue()))->setTitle($tabName);

        $this->tabs[] = $tab;

        return $tab;
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
        if(!$this->footerTrigger){
            $this->footer('<button class="layui-btn" lay-submit="" lay-filter="'.$this->getId().'-submit">立即提交</button>');
        }

        //所有组件的渲染
        $assemblys = join("\n", array_map(function(ThinkerLayout $value){
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
     * @title      send
     * @description
     * @createtime 2019/11/15 6:49 下午
     * @param               $title
     * @param callable|null $callable
     * @return string
     * @author     yangyuance
     */
    public function send($title, callable $callable = null)
    {
        if (is_callable($callable)) {
            ThinkerAdmin::send()->html(
                (new ThinkerPage(function($page) use($callable){
                    call($callable, [$page, $this]);
                }))->setTitle($title)->render()
            );
        } else {
            ThinkerAdmin::send()->html(
                (new ThinkerPage(function(ThinkerPage $page){
                    $page->card()->setBodyLayout($this);
                }))->setTitle($title)->render()
            );
        }
    }
}