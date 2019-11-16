<?php


namespace Yirius\Admin\form;


use Yirius\Admin\extend\ThinkerLayout;
use Yirius\Admin\layout\ThinkerPage;
use Yirius\Admin\ThinkerAdmin;

class ThinkerForm extends ThinkerLayout
{
    use setExtend;

    public function __construct(callable $callable = null)
    {
        parent::__construct();

        //judge thinkeradmin's config extends
        if (config('thinkeradmin.form.extends')) {
            $this->setExtends(config('thinkeradmin.form.extends'));
        }

        if(is_callable($callable)){
            call($callable, [$this]);
        }
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
        //splicing assembly html
        $splicingHtml = "";
        foreach ($this->assemblys as $i => $v) {
            $splicingHtml .= '<div class="layui-form-item">' . $v->render() . '</div>';
        }


        //return all string
        return <<<HTML
<form class="layui-form" lay-filter="{$this->getId()}" id="{$this->getId()}">
{$splicingHtml}
</form>
HTML
            ;
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