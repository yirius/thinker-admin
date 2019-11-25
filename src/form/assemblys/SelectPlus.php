<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午11:26
 */

namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class SelectPlus
 * @package Yirius\Admin\form\assemblys
 * @method SelectPlus setData(array $data);
 * @method SelectPlus setContent($html);
 * @method SelectPlus setTips($tips);
 * @method SelectPlus setEmpty($empty);
 * @method SelectPlus setFilterable(bool $search);
 * @method SelectPlus setSearchTips($tips);
 * @method SelectPlus setDelay(int $delay);
 * @method SelectPlus setDirection($direction); 	auto / up / down
 * @method SelectPlus setStyle(array $style);
 * @method SelectPlus setHeight(int $height);
 * @method SelectPlus setPaging(bool $paging);
 * @method SelectPlus setPageSize(int $pageSize);
 * @method SelectPlus setPageEmptyShow(bool $PageEmptyShow);
 * @method SelectPlus setPageRemote(bool $Remote);
 * @method SelectPlus setRadio(bool $radio);
 * @method SelectPlus setRepeat(bool $repeat);
 * @method SelectPlus setClickClose(bool $clickClose);
 * @method SelectPlus setMax(int $max);
 * @method SelectPlus setName($name);
 * @method SelectPlus setShowCount(int $showCount);
 * @method SelectPlus setAutoRow(bool $autoRow);
 * @method SelectPlus setSize($size); large / medium / small / mini
 * @method SelectPlus setDisabled(bool $disabled);
 * @method SelectPlus setRemoteSearch(bool $search);
 */
class SelectPlus extends Assembly
{
    /**
     * @title afterSetForm
     * @description
     * @createtime 2019/2/24 下午11:28
     */
    protected function _init()
    {
        ThinkerAdmin::script('selectplus', false, true);
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
        $jsonConfig = json_encode($this->getConfig());

        $value = $this->getValue();
        if(!is_array($value)){
            $value = explode(",", $value);
        }
        $value = json_encode($value);

        ThinkerAdmin::script(<<<HTML
var demo1 = layui.selectplus.render($.extend({
    el: '#{$this->getId()}',
    language: 'zn',
    prop: {
		name: 'text',
		value: 'value',
	},
    initValue: {$value},
    data: []
}, {$jsonConfig}))
HTML
        );

        return <<<HTML
{$this->getLabel()}
<div class="{$this->getClass()}">
    <div name="{$this->getField()}" id="{$this->getId()}" lay-filter="{$this->getId()}" {$this->getAttrs()}></div>
</div>
HTML;
    }
}