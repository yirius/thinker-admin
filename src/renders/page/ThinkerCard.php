<?php


namespace Yirius\Admin\renders\page;


use Yirius\Admin\support\abstracts\LayoutAbstract;

class ThinkerCard extends LayoutAbstract
{
    private $headerLayout = [];
    private $bodyLayout = [];

    private $cardHeaderClass = [];
    private $cardBodyClass = [];

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
     * @title      addHeaderLayout
     * @description
     * @createtime 2020/5/27 4:56 下午
     * @param LayoutAbstract $headerLayout
     * @return $this
     * @author     yangyuance
     */
    public function addHeaderLayout(LayoutAbstract $headerLayout) {
        if($headerLayout != null) {
            $this->headerLayout[] = $headerLayout;
        }
        return $this;
    }

    /**
     * @title      addBodyLayout
     * @description
     * @createtime 2020/5/27 4:56 下午
     * @param LayoutAbstract $bodyLayout
     * @return $this
     * @author     yangyuance
     */
    public function addBodyLayout(LayoutAbstract $bodyLayout) {
        if($bodyLayout != null) {
            $this->bodyLayout[] = $bodyLayout;
        }
        return $this;
    }

    /**
     * @title      addcardHeaderClass
     * @description
     * @createtime 2020/5/27 4:57 下午
     * @param $cardHeaderClass
     * @return $this
     * @author     yangyuance
     */
    public function addCardHeaderClass($cardHeaderClass) {
        $this->cardHeaderClass[] = $cardHeaderClass;
        return $this;
    }

    /**
     * @title      addCardBodyClass
     * @description
     * @createtime 2020/5/27 4:57 下午
     * @param $cardBodyClass
     * @return $this
     * @author     yangyuance
     */
    public function addCardBodyClass($cardBodyClass) {
        $this->cardBodyClass[] = $cardBodyClass;
        return $this;
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
        $cardBodyClass = join(" ", $this->cardBodyClass);

        $headerLayout = "";
        if(!empty($this->headerLayout)) {
            $cardHeaderClass = join(" ", $this->cardHeaderClass);
            $headerLayout = join("", array_map(function (LayoutAbstract $abstract) {
                return $abstract->render() . "\n";
            }, $this->headerLayout));
            $headerLayout = "<div class=\"layui-card-header ".$cardHeaderClass."\">".$headerLayout."</div>";
        }

        $bodyLayout = join("", array_map(function (LayoutAbstract $abstract) {
            return $abstract->render() . "\n";
        }, $this->bodyLayout));

        return <<<HTML
<div class="layui-card {$this->getClassString()}" {$this->getAttrString()}>
    {$headerLayout}
    <div class="layui-card-body {$cardBodyClass}">
    {$bodyLayout}
    </div>
</div>
HTML;

    }
}