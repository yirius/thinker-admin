<?php


namespace Yirius\Admin\layout;


use Yirius\Admin\extend\ThinkerLayout;

/**
 * Class ThinkerBreadcrumb
 * @package Yirius\Admin\layout
 */
class ThinkerBreadcrumb extends ThinkerLayout
{
    /**
     * @var string
     */
    protected $homeText = "首页";

    /**
     * @var array
     */
    protected $breadcrumbs = [];

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
     * @title      setHomeText
     * @description
     * @createtime 2019/11/16 6:02 下午
     * @param $homeText
     * @return $this
     * @author     yangyuance
     */
    public function setHomeText($homeText)
    {
        $this->homeText = $homeText;

        return $this;
    }

    /**
     * @title      getHomeText
     * @description
     * @createtime 2019/11/16 6:02 下午
     * @return string
     * @author     yangyuance
     */
    public function getHomeText()
    {
        return $this->homeText;
    }

    /**
     * @title      setBreadcrumbs
     * @description
     * @createtime 2019/11/16 6:04 下午
     * @param array $breadcrumbs
     * @return $this
     * @author     yangyuance
     */
    public function setBreadcrumbs(array $breadcrumbs)
    {
        $this->breadcrumbs = array_map(function($value){
            if(is_array($value)){
                if(!isset($value['text'])){
                    $value['text'] = $value[array_keys($value)[0]];
                }
                return $value;
            }else if(is_callable($value)){
                return call($value);
            }else{
                return ['text' => $value];
            }
        }, $breadcrumbs);

        return $this;
    }

    /**
     * @return array
     */
    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
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
        $layouts = [];

        for($i = 0; $i < count($this->breadcrumbs); $i++){
            if(empty($this->breadcrumbs[$i]['value'])){
                $layouts[] = '<a><cite>' . $this->breadcrumbs[$i]['text'] . '</cite></a>';
            }else{
                $layouts[] = '<a thinker-href="' . $this->breadcrumbs[$i]['value'] . '"><cite>' . $this->breadcrumbs[$i]['text'] . '</cite></a>';
            }
        }

        $layouts = join("\n", $layouts);

        return <<<HTML
<div class="layui-breadcrumb thinker-breadcrumb" lay-filter="thinker-breadcrumb">
    <a thinker-href="/">{$this->homeText}</a>
    {$layouts}
</div>
HTML;
    }
}