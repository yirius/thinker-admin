<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/9
 * Time: 下午11:22
 */

namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\form\Assembly;
use Yirius\Admin\ThinkerAdmin;

class ColorPicker extends Assembly
{
    /**
     * @title format
     * @description
     * @createtime 2019/3/9 下午11:50
     * @param string $type
     * @return $this
     * @throws \Exception
     */
    public function format($type = "rgb")
    {
        $this->setAttrs("data-format", $type);

        return $this;
    }

    /**
     * @title alpha
     * @description
     * @createtime 2019/3/9 下午11:50
     * @return $this
     * @throws \Exception
     */
    public function alpha()
    {
        $this->setAttrs("data-alpha", 'true');

        return $this;
    }

    /**
     * @title colors
     * @description
     * @createtime 2019/3/9 下午11:51
     * @param array $colors
     * @return $this
     * @throws \Exception
     */
    public function colors(array $colors)
    {
        $this->setAttrs("data-predefine", 'true');

        $this->setAttrs("data-colors", htmlspecialchars(json_encode($colors)));

        return $this;
    }

    /**
     * @title size
     * @description
     * @createtime 2019/3/9 下午11:52
     * @param string $size
     * @return $this
     * @throws \Exception
     */
    public function size($size = "lg")
    {
        $this->setAttrs("data-size", $size);

        return $this;
    }

    /**
     * @title onChange
     * @description
     * @createtime 2019/3/10 上午12:04
     * @param $callback
     * @return $this
     * @throws \Exception
     */
    public function onChange($callback)
    {
        $this->setAttrs("data-change", $callback);

        return $this;
    }

    /**
     * @title onChange
     * @description
     * @createtime 2019/3/10 上午12:04
     * @param $callback
     * @return $this
     * @throws \Exception
     */
    public function onDone($callback)
    {
        $this->setAttrs("data-done", $callback);

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
        ThinkerAdmin::script("colorpicker", false, true);

        return <<<HTML
{$this->getLabel()}
<div class="{$this->getClass()}">
    <div id="{$this->getId()}" lay-filter="{$this->getId()}" data-color="{$this->getValue()}" {$this->getAttrs()} lay-colorpicker=""></div>
    <input type="hidden" id="{$this->getId()}_hidden" name="{$this->getField()}" value="{$this->getValue()}"/>
</div>
HTML;
    }
}