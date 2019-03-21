<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午5:16
 */

namespace Yirius\Admin\widgets;


class Breadcrumb
{
    /**
     * 面包屑导航
     * use for pageview top breadcrumb
     * if empty, pageview not show breadcrumb
     *
     * @var array
     */
    protected $breadcrumb = [];

    /**
     * @var string
     */
    protected $homeText = "主页";

    /**
     * Breadcrumb constructor.
     * @param array|null $breadcrumb
     * @throws \Exception
     */
    public function __construct($breadcrumb = null)
    {
        if(!is_null($breadcrumb)){
            if($breadcrumb instanceof \Closure){
                call($breadcrumb, [$this]);
            }else{
                if(is_array($breadcrumb)){
                    $this->setBreadcrumb($breadcrumb);
                }
            }
        }
    }

    /**
     * @title setHomeText
     * @description
     * @createtime 2019/2/24 下午5:23
     * @param $homeText
     * @return $this
     */
    public function setHomeText($homeText)
    {
        $this->homeText = $homeText;

        return $this;
    }

    /**
     * @title setBreadcrumb
     * @description use for set pageview breadcrumb
     * @createtime 2019/1/30 下午2:52
     * @param array $breadcrumb
     * @return $this
     * @throws \Exception
     */
    public function setBreadcrumb(array $breadcrumb)
    {

        $this->validateBreadcrumb($breadcrumb);

        $this->breadcrumb = $breadcrumb;

        return $this;
    }

    /**
     * @title validateBreadcrumb
     * @description use for validate breadcrumb is a validate array
     * @createtime 2019/1/30 下午2:51
     * @param array $breadcrumbs
     * @throws \Exception
     */
    protected function validateBreadcrumb(array $breadcrumbs)
    {
        foreach ($breadcrumbs as $breadcrumb) {
            if (!is_array($breadcrumb) || !array_key_exists("text", $breadcrumb)) {
                throw new \Exception(lang("breadcrumb keys error"));
            }
        }
    }

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        //make breadcrumb
        if(empty($this->breadcrumb)){
            return '';
        }else{
            $html = [];
            foreach($this->breadcrumb as $value){
                if(empty($value['key'])){
                    $html[] = '<a><cite>' . $value['text'] . '</cite></a>';
                }else{
                    $html[] = '<a thinker-href="' . $value['key'] . '">' . $value['text'] . '</a>';
                }
            }
            $breadcrumb = join("", $html);
            return <<<HTML
<div class="layui-card thinkeradmin-header">
    <div class="layui-breadcrumb" lay-filter="breadcrumb">
        <a thinker-href="/">{$this->homeText}</a>
        {$breadcrumb}
    </div>
</div>
HTML;
        }
    }

}