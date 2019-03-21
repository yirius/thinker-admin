<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/18
 * Time: 下午12:05
 */

namespace Yirius\Admin\widgets;


use Yirius\Admin\Layout;

class Card extends Layout
{
    /**
     * @var null
     */
    protected $title = null;

    /**
     * @var string
     */
    protected $headerClass = 'thinkeradmin-card-header-auto';

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var string
     */
    protected $contentClass = '';

    /**
     * Card constructor.
     * @param null $title
     * @param null $content
     */
    function __construct($title = null, $content = null)
    {
        if (!is_null($title)) {
            if($title instanceof \Closure){
                call($title, [$this]);
            }else{
                $this->setTitle($title);
            }
        }

        if (!is_null($content)) {
            $this->setContent($content);
        }
    }

    /**
     * @title setTitle
     * @description
     * @createtime 2019/2/18 下午12:37
     * @param $title
     * @return Card
     */
    public function setTitle($title)
    {
        if ($title instanceof \Closure) {
            $this->title = $title($this);
        } else {
            $this->title = $title;
        }
        return $this;
    }

    /**
     * @title setHeaderClass
     * @description
     * @createtime 2019/2/27 下午12:15
     * @param $headerClass
     * @return $this
     */
    public function setHeaderClass($headerClass)
    {
        $this->headerClass = $headerClass;

        return $this;
    }

    /**
     * @title setContent
     * @description set card content
     * @createtime 2019/2/18 下午12:07
     * @param $content
     * @return Card
     */
    public function setContent($content)
    {
        if ($content instanceof \Closure) {
            $this->content = $content($this);
        } else {
            $this->content = $content;
        }
        return $this;
    }

    /**
     * @title setContentClass
     * @description
     * @createtime 2019/2/27 下午12:15
     * @param $contentClass
     * @return $this
     */
    public function setContentClass($contentClass)
    {
        $this->contentClass = $contentClass;

        return $this;
    }

    /**
     * @title render
     * @description use for render each type
     * @createtime 2019/1/30 下午3:10
     * @return mixed
     */
    public function render()
    {
        //judge if there have title
        if(!is_null($this->title)){
            $header = <<<HTML
<div class="layui-card-header {$this->headerClass}">{$this->title}</div>
HTML;

        }else{
            $header = '';
        }

        //render page
        return <<<HTML
<div class="layui-card">
    {$header}
    <div class="layui-card-body {$this->contentClass}">{$this->content}</div>
</div>
HTML;
    }

}