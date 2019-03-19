<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/2
 * Time: 下午6:51
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\Admin;
use Yirius\Admin\form\Assembly;

/**
 * Class WangEditor
 * @method WangEditor setMenus(array $menus);
 * @method WangEditor setDebug(bool $isDebug);
 * @method WangEditor setZIndex(int $zIndex);
 * @method WangEditor setLang(array $lang);
 * @method WangEditor setPasteFilterStyle(bool $isFilter);
 * @method WangEditor setPasteIgnoreImg(bool $isIgnore);
 * @method WangEditor setPasteTextHandle($callback);
 * @method WangEditor setShowLinkImg(bool $isShow);
 * @method WangEditor setLinkImgCallback($callback);
 * @method WangEditor setLinkCheck($callback);
 * @method WangEditor setLinkImgCheck($callback);
 * @method WangEditor setOnfocus($callback);
 * @method WangEditor setOnblur($callback);
 * @method WangEditor setColors(array $colors);
 * @method WangEditor setEmotions(array $emotions);
 * @method WangEditor setFontNames(array $fontNames);
 * upload image info
 * @method WangEditor setUploadImgShowBase64(bool $isBase);
 * @method WangEditor setUploadImgServer($server);
 * @method WangEditor setUploadImgMaxSize(int $size);
 * @method WangEditor setUploadImgMaxLength(int $max);
 * @method WangEditor setUploadImgParams(array $params);
 * @method WangEditor setUploadImgParamsWithUrl(bool $isWithUrl);
 * @method WangEditor setUploadFileName($fileName);
 * @method WangEditor setUploadImgHeaders(array $header);
 * @method WangEditor setWithCredentials(bool $isBase);
 * @method WangEditor setUploadImgTimeout(int $timeout);
 *
 * @method WangEditor getMenus();
 * @method WangEditor getDebug();
 * @method WangEditor getZIndex();
 * @method WangEditor getLang();
 * @method WangEditor getPasteFilterStyle();
 * @method WangEditor getPasteIgnoreImg();
 * @method WangEditor getPasteTextHandle();
 * @method WangEditor getShowLinkImg();
 * @method WangEditor getLinkImgCallback();
 * @method WangEditor getLinkCheck();
 * @method WangEditor getLinkImgCheck();
 * @method WangEditor getOnfocus();
 * @method WangEditor getOnblur();
 * @method WangEditor getColors();
 * @method WangEditor getEmotions();
 * @method WangEditor getFontNames();
 * upload image info
 * @method WangEditor getUploadImgShowBase64();
 * @method WangEditor getUploadImgServer();
 * @method WangEditor getUploadImgMaxSize();
 * @method WangEditor getUploadImgMaxLength();
 * @method WangEditor getUploadImgParams();
 * @method WangEditor getUploadImgParamsWithUrl();
 * @method WangEditor getUploadFileName();
 * @method WangEditor getUploadImgHeaders();
 * @method WangEditor getWithCredentials();
 * @method WangEditor getUploadImgTimeout();
 *
 * @package Yirius\Admin\form\assembly
 */
class WangEditor extends Assembly
{
    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        $value = $this->value;
        if($this->value === strip_tags($this->value)){
            $value = "<p>" . $value . "</p>";
        }

        return <<<HTML
<label class="layui-form-label">{$this->getLabel()}</label>
<div class="{$this->getClass()}">
    <textarea name="{$this->getName()}" style="display: none">{$this->getValue()}</textarea>
    <div id="{$this->getId()}" lay-filter="{$this->getId()}" lay-wangeditor="" {$this->getAttributes()} >{$value}</div>
</div>
HTML;
    }

    /**
     * @title afterSetForm
     * @description
     * @createtime 2019/3/2 下午7:05
     */
    protected function afterSetForm()
    {
        Admin::script("wangEditor", 2);

        $this->setZIndex(0)->setUploadImgServer("./thinkeradmin/uploads?isimage=1");
    }
}