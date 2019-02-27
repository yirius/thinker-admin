<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/25
 * Time: 下午11:21
 */

namespace Yirius\Admin\form;


use Yirius\Admin\Admin;
use Yirius\Admin\Layout;

class Footer extends Layout
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var string
     */
    protected $footer = '';

    /**
     * Inline constructor.
     * @param Form $form
     * @param \Closure|null $callback
     */
    public function __construct(Form $form, \Closure $callback = null)
    {
        $this->form = $form;

        if($callback instanceof \Closure){
            call($callback, [$this]);
        }
    }

    /**
     * @title setSubmit
     * @description
     * @createtime 2019/2/25 下午6:28
     * @param $url
     * @param null $successCall
     * @param null $beforeSubmit
     * @return $this
     */
    public function submit($url, $successCall = null, $beforeSubmit = null)
    {
        $doneCall = is_null($successCall) ? 'layer.msg(res.msg);' : $successCall;

        $beforeEvent = is_null($beforeSubmit) ? '' : htmlspecialchars($beforeSubmit);

        Admin::script(<<<HTML
layui.form.on("submit({$this->form->getName()}-submit)", function (obj) {
    var beforeEvent = '{$beforeEvent}';
    if(beforeEvent){
        beforeEvent = new Function('return function(obj){' + beforeEvent + "}")();
        obj = beforeEvent(obj);
    }
    layui.http.request({
        method: 'POST',
        url: "{$url}",
        data: layui.http._beforeAjax(obj.field),
        success: function (res) {
            {$doneCall}
        }
    });
    return false;
});
HTML
        );

        return $this;
    }

    /**
     * @title setFooter
     * @description
     * @createtime 2019/2/25 下午11:35
     * @param $footer
     * @return $this
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * @return string
     */
    public function getFooter()
    {
        return empty($this->footer) ? '<button class="layui-btn" lay-submit="" lay-filter="'.$this->form->getName().'-submit">立即提交</button>' : $this->footer;
    }

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        return <<<HTML
<div class="layui-form-item">
    <div class="layui-input-block">
        <div class="layui-footer">
            {$this->getFooter()}
        </div>
    </div>
</div>
HTML;
    }
}