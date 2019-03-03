<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/25
 * Time: 下午3:25
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\Admin;
use Yirius\Admin\form\Assembly;

class Upload extends Assembly
{
    /**
     * @var string
     */
    protected $url = "./thinkeradmin/uploads?isimage=1";

    /**
     * @title isFile
     * @description
     * @createtime 2019/2/25 下午4:51
     */
    public function isFile()
    {
        $this->url = "./thinkeradmin/uploads";

        return $this;
    }

    /**
     * @title url
     * @description
     * @createtime 2019/2/25 下午4:51
     * @param $url
     */
    public function url($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @title multi
     * @description
     * @createtime 2019/2/25 下午5:02
     * @throws \Exception
     */
    public function multi()
    {
        $this->setAttributes("data-ismulti", "true");

        return $this;
    }

    /**
     * @title offFancybox
     * @description
     * @createtime 2019/2/25 下午6:01
     * @return $this
     */
    public function offFancybox()
    {
        $this->offAttributes("data-isshow");
        return $this;
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
<label class="layui-form-label">{$this->getLabel()}</label>
<div class="{$this->getClass()}">
    <button type="button" class="layui-btn layui-btn-primary" lay-upload="" data-url="{$this->url}" {$this->getAttributes()} data-name="{$this->getName()}" id="{$this->getId()}" >上传图片</button>
    <div class="thinkeradmin-upload-list"></div>
</div>
HTML;
    }

    /**
     * @title afterSetForm
     * @description
     * @createtime 2019/2/25 下午11:19
     * @throws \Exception
     */
    protected function afterSetForm()
    {
        Admin::script('upload', 2);

        $this->setAttributes('data-isshow', 'true');
    }
}