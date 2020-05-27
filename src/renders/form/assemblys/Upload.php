<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\renders\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class Upload
 * @package Yirius\Admin\renders\form\assemblys
 * @method Upload setUrl($url)
 * @method Upload setAccept($accept)
 * @method Upload setHeader(array $header)
 * @method Upload setAcceptMime($mime)
 * @method Upload setExts($exts)
 * @method Upload setSize($size)
 * @method Upload setMultiple(bool $bool)
 * @method Upload setNumber($num)
 * @method Upload setName($name)
 * @method Upload setShow(bool $name)
 *
 * @method Upload getUrl()
 * @method Upload getAccept()
 * @method Upload getHeader()
 * @method Upload getAcceptMime()
 * @method Upload getExts()
 * @method Upload getSize()
 * @method Upload getMultiple()
 * @method Upload getNumber()
 * @method Upload getName()
 * @method Upload getShow()
 */
class Upload extends Assembly
{
    protected $attrsFields = [
        "url", "accept", "header", "acceptMime", "exts", "size", "multiple", "number", "name", "show"
    ];

    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        $this->attrs['show'] = true;
        $this->attrs['multiple'] = false;

        ThinkerAdmin::script("upload", false, true);

        $this->setName($this->getField())
            ->setUrl("./thinkeradmin/admin/upload")->addAttr("lay-upload", "");

        $this->addClass("layui-btn")
            ->addClass("layui-btn-sm")
            ->addClass("margin-top-xs")
            ->removeClass("layui-input-block");

    }

    /**
     * @title      isFile
     * @description
     * @createtime 2020/5/27 4:32 下午
     * @return Upload
     * @author     yangyuance
     */
    public function isFile()
    {
        return $this->setUrl("./thinkeradmin/admin/upload?isimage=0")->setAccept("file")->setText("文件上传");
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
        return $this->getLabel() . "\n" .
            "<div class=\"layui-input-block\">\n" .
            "    <a class=\"" . $this->getClassString() . "\" id=\"" . $this->getId() . "\" lay-filter=\"" . $this->getId() . "\" name=\"" . $this->getField() . "\" " . $this->getAttrString() . ">" . $this->getText() . "</a>\n" .
            "    " . $this->getButtonHtml() . "\n" .
            "</div>";
    }

    /**
     * @return string
     */
    public function getButtonHtml()
    {
        $value = explode(",", $this->getValue());

        $imgs = "";

        //判断是否是多文件
        if ($this->getMultiple()) $suffix = "[]"; else $suffix = "";

        foreach ($value as $i => $v) {
            if (!empty($v) && $v != ",") {
                $count = $i . 1;
                if (strpos($v, ".jpg") !== false ||
                    strpos($v, ".jpeg") !== false ||
                    strpos($v, ".png") !== false ||
                    strpos($v, ".bmp") !== false
                ) {
                    $showHtml = '<img src="' . $v . '" class="img" href="' . $v . '" lay-photos="">';
                } else {
                    $showHtml = '<a href="' . $v . '">' . $v . '</a>';
                }

                $imgs .= <<<HTML
<dd class="item_img" id="thinkeradmin_upload_{$count}">
    <div class="operate">
        <i class="thinkeradmin-upload-close layui-icon layui-icon-delete"></i>
    </div>
    {$showHtml}
    <input type="hidden" name="{$this->getField()}{$suffix}" value="{$v}">
</dd>
HTML;

            }
        }

        return <<<HTML
<div class="thinkeradmin-upload-list">
<input type="hidden" name="{$this->getField()}{$suffix}" value="">
{$imgs}
</div>
HTML;
    }
}