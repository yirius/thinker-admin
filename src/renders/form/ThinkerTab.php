<?php


namespace Yirius\Admin\renders\form;


use Yirius\Admin\support\abstracts\LayoutAbstract;

class ThinkerTab extends ThinkerAssemblys
{
    protected $title;

    /**
     * @title      setTitle
     * @description
     * @createtime 2020/5/27 4:40 下午
     * @param $title
     * @return $this
     * @author     yangyuance
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function __construct(callable $closure = null, $useValue = [])
    {
        parent::__construct();

        $this->setUseValue($useValue);

        if(is_callable($closure)) call($closure, [$this]);
    }

    /**
     * @title      inline
     * @description
     * @createtime 2020/5/27 4:41 下午
     * @param callable|null $closure
     * @return ThinkerInline
     * @author     yangyuance
     */
    public function inline(callable $closure = null) {
        $inline = new ThinkerInline($closure, $this->getUseValue());
        $this->addAssemblys($inline);
        return $inline;
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
        return join("\n", array_map(function(LayoutAbstract $value){
            return '<div class="layui-form-item">' . ($value->render()) . "</div>";
        }, $this->assemblys));
    }
}