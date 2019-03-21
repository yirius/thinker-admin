<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/25
 * Time: 下午3:25
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\Admin;

/**
 * Class Upload
 * @method Upload setUrl($url)
 * @method Upload setAccept($accept)
 * @method Upload setHeader(array $header)
 * @method Upload setAcceptMime($mime)
 * @method Upload setExts($exts)
 * @method Upload setSize($size)
 * @method Upload setMultiple(bool $bool)
 * @method Upload setNumber($num)
 *
 * @method Upload getUrl()
 * @method Upload getAccept()
 * @method Upload getHeader()
 * @method Upload getAcceptMime()
 * @method Upload getExts()
 * @method Upload getSize()
 * @method Upload getMultiple()
 * @method Upload getNumber()
 * @package Yirius\Admin\form\assembly
 */
class Upload extends Button
{
    /**
     * @var string
     */
    protected $text = "上传图片";

    /**
     * @var string
     */
    protected $buttonHtml = '<div class="thinkeradmin-upload-list"></div>';

    /**
     * @title isFile
     * @description
     * @createtime 2019/3/3 下午10:05
     * @return Upload
     */
    public function isFile()
    {
        return $this->setUrl("./thinkeradmin/uploads?isimage=0")->setAccept("file");
    }

    /**
     * @title multi
     * @description
     * @createtime 2019/2/25 下午5:02
     * @throws \Exception
     */
    public function multi()
    {
        $this->setMultiple(true);

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
     * @title setName
     * @description
     * @createtime 2019/3/21 下午8:06
     * @param $name
     * @return $this|Button
     * @throws \Exception
     */
    public function setName($name)
    {
        $this->name = $name;

        $this->setAttributes("data-name", $this->name);

        return $this;
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

        $this
            ->setAttributes('data-isshow', 'true')
            ->setAttributes("lay-upload", "")
            ->setAttributes("data-name", $this->name)
            ->setUrl("./thinkeradmin/uploads");
    }

    /**
     * @return string
     */
    public function getButtonHtml()
    {
        $value = explode(",", $this->getValue());

        $imgs = "";

        foreach($value as $i => $v){
            if(!empty($v)){
                $count = $i + 1;
                $imgs .= <<<HTML
<dd class="item_img" id="thinkeradmin_upload_{$count}">
    <div class="operate">
        <i class="thinkeradmin-upload-close layui-icon layui-icon-delete"></i>
    </div>
    <img src="{$v}" class="img" href="{$v}" data-fancybox="">
    <input type="hidden" name="{$this->getName()}" value="{$v}">
</dd>
HTML;

            }
        }

        return <<<HTML
<div class="thinkeradmin-upload-list">
{$imgs}
</div>
HTML
;
    }
}