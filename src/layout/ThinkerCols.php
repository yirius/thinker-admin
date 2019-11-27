<?php


namespace Yirius\Admin\layout;


use Yirius\Admin\extend\ThinkerLayout;

/**
 * Class ThinkerCols
 * @package Yirius\Admin\layout
 * @method ThinkerCols xs(int $value);
 * @method ThinkerCols sm(int $value);
 * @method ThinkerCols md(int $value);
 * @method ThinkerCols lg(int $value);
 *
 * @method ThinkerCols xsOffset(int $value);
 * @method ThinkerCols smOffset(int $value);
 * @method ThinkerCols mdOffset(int $value);
 * @method ThinkerCols lgOffset(int $value);
 *
 * @method ThinkerCols xsBlock();
 * @method ThinkerCols smBlock();
 * @method ThinkerCols mdBlock();
 * @method ThinkerCols lgBlock();
 *
 * @method ThinkerCols xsInline();
 * @method ThinkerCols smInline();
 * @method ThinkerCols mdInline();
 * @method ThinkerCols lgInline();
 *
 * @method ThinkerCols xsInlineBlock();
 * @method ThinkerCols smInlineBlock();
 * @method ThinkerCols mdInlineBlock();
 * @method ThinkerCols lgInlineBlock();
 *
 * @method ThinkerCols xsHide();
 * @method ThinkerCols smHide();
 * @method ThinkerCols mdHide();
 * @method ThinkerCols lgHide();
 */
class ThinkerCols extends ThinkerLayout
{
    use setLayout;

    /**
     * ThinkerRows constructor.
     * @param callable|null $callable
     */
    public function __construct(callable $callable = null)
    {
        parent::__construct();

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
        $layouts = join("\n", $this->layouts);

        return <<<HTML
<div class="{$this->getClass()}" {$this->getAttrs()}>
{$layouts}
</div>
HTML;
    }

    /**
     * @title      __call
     * @description
     * @createtime 2019/11/15 2:28 下午
     * @param $name
     * @param $arguments
     * @return $this|mixed|string|ThinkerLayout
     * @author     yangyuance
     */
    public function __call($name, $arguments)
    {
        //一些默认操作，不需要重写了
        if(in_array($name, ['xs', 'sm', 'md', 'lg'])){
            $this->setClass("layui-col-" . $name . (isset($arguments[0]) ? intval($arguments[0]) : 6));
            return $this;
        }else if(in_array($name, ['xsOffset', 'smOffset', 'mdOffset', 'lgOffset', 'spaceOffset'])){
            $this->setClass("layui-col-".substr($name, 0, 2)."-offset" . (isset($arguments[0]) ? intval($arguments[0]) : 6));
            return $this;
        }else if(in_array($name, ['xsBlock', 'smBlock', 'mdBlock', 'lgBlock'])){
            $this->setClass("layui-show-".substr($name, 0, 2)."-block");
            return $this;
        }else if(in_array($name, ['xsInline', 'smInline', 'mdInline', 'lgInline'])){
            $this->setClass("layui-show-".substr($name, 0, 2)."-inline");
            return $this;
        }else if(in_array($name, ['xsInlineBlock', 'smInlineBlock', 'mdInlineBlock', 'lgInlineBlock'])){
            $this->setClass("layui-show-".substr($name, 0, 2)."-inline-block");
            return $this;
        }else if(in_array($name, ['xsHide', 'smHide', 'mdHide', 'lgHide'])){
            $this->setClass("layui-hide-".substr($name, 0, 2));
            return $this;
        }

        return parent::__call($name, $arguments); // TODO: Change the autogenerated stub
    }
}