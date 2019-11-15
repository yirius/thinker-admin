<?php


namespace Yirius\Admin\layout;


use Yirius\Admin\extend\ThinkerLayout;

class ThinkerCard extends ThinkerLayout
{
    use setLayout;

    /**
     * 卡片的class
     * @var array
     */
    protected $cardClass = [
        'header' => [],
        'body' => []
    ];

    /**
     * ThinkerRows constructor.
     * @param callable|null $callable
     */
    public function __construct(callable $callable = null)
    {
        parent::__construct();

        if(!isset($this->layouts['header'])) $this->layouts['header'] = [];
        if(!isset($this->layouts['body'])) $this->layouts['body'] = [];

        if(is_callable($callable)){
            call($callable, [$this]);
        }
    }

    /**
     * @title      setHeaderLayout
     * @description
     * @createtime 2019/11/15 2:57 下午
     * @param $layout
     * @return ThinkerCard
     * @author     yangyuance
     */
    public function setHeaderLayout($layout)
    {
        return $this->setLayouts($layout, "header");
    }

    /**
     * @title      setBodyLayout
     * @description
     * @createtime 2019/11/15 2:59 下午
     * @param $layout
     * @return ThinkerCard
     * @author     yangyuance
     */
    public function setBodyLayout($layout)
    {
        return $this->setLayouts($layout, "body");
    }

    /**
     * @title      setCardClass
     * @description 设置卡片的类
     * @createtime 2019/11/15 3:00 下午
     * @param        $cardClass
     * @param string $field
     * @return $this
     * @author     yangyuance
     */
    public function setCardClass($cardClass, $field = 'body')
    {
        $this->cardClass[$field][] = is_array($cardClass) ? join(" ", $cardClass) : $cardClass;

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
        //判断header是否存在
        $headerLayout = "";
        if(!empty($this->layouts['header'])){
            $headerLayout = '<div class="layui-card-header '.join(" ", $this->cardClass['header']).'">'.join("\n", $this->layouts['header']).'</div>';
        }

        //Layout
        $bodyLayout = join("\n", $this->layouts['body']);
        $bodyClass = join(" ", $this->cardClass['body']);

        return <<<HTML
<div class="layui-card {$this->getClass()}" {$this->getAttrs()}>
    {$headerLayout}
    <div class="layui-card-body {$bodyClass}">
    {$bodyLayout}
    </div>
</div>
HTML;

    }
}