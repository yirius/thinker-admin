<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/25
 * Time: 下午3:25
 */

namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\Admin;
use Yirius\Admin\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

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
class Upload extends Assembly
{
    protected $class = ['layui-btn', 'layui-btn-lg'];

    /**
     * @title isFile
     * @description
     * @createtime 2019/3/3 下午10:05
     * @return Upload
     */
    public function isFile()
    {
        return $this->setUrl("./thinkeradmin/Admin/upload?isimage=0")->setAccept("file")->setText("上传文件");
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

        $this->setAttrs("data-name", $this->name);

        return $this;
    }

    /**
     * @return string
     */
    public function getButtonHtml()
    {
        $value = explode(",", $this->getValue());

        $imgs = "";

        //判断是否是多文件
        if($this->getMultiple()) $suffix = "[]"; else $suffix = "";

        foreach($value as $i => $v){
            if(!empty($v)){
                $count = $i + 1;
                //判断是图片还是其他文件
                $isImage = getimagesize(strpos($v, "http") !== false ? $v : env("root_path") . "public" . DS . $v);
                if($isImage){
                    $showHtml = '<img src="'.$v.'" class="img" href="'.$v.'" data-fancybox="">';
                }else{
                    $showHtml = '<a href="'.$v.'">' . $v . '</a>';
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
HTML
;
    }

    /**
     * @title      _init
     * @description
     * @createtime 2019/11/25 11:03 下午
     * @author     yangyuance
     */
    protected function _init()
    {
        ThinkerAdmin::script("upload", false, true);

        $this
            ->setAttrs('data-isshow', 'true')
            ->setAttrs("lay-upload", "")
            ->setAttrs("data-name", $this->getField())
            ->setUrl("./thinkeradmin/Admin/upload");
    }

    /**
     * @title      render
     * @description
     * @createtime 2019/11/25 11:00 下午
     * @return string
     * @author     yangyuance
     */
    public function render()
    {
        $this->setAttrs("lay-upload", "");

        foreach($this->getConfig() as $i => $v){
            if(!in_array($i, ['field', 'value'])){
                $this->setAttrs("data-".$i, $v);
            }
        }

        return <<<HTML
{$this->getLabel()}
<div class="layui-input-block">
    <a class="{$this->getClass()}" id="{$this->getId()}" lay-filter="{$this->getId()}" name="{$this->getField()}" {$this->getAttrs()}>{$this->getText()}</a>
    {$this->getButtonHtml()}
</div>
HTML;
    }
}