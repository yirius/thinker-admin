<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午6:17
 */

namespace Yirius\Admin\form;


class Tab extends Inline
{
    protected $title = "";

    /**
     * Tab constructor.
     * @param Form $form
     * @param $tabName
     * @param \Closure|null $callback
     */
    public function __construct(Form $form, $tabName, \Closure $callback = null)
    {
        $this->form = $form;

        $this->title = $tabName;

        if($callback instanceof \Closure){
            call($callback, [$this]);
        }
    }

    /**
     * @title setTitle
     * @description
     * @createtime 2019/3/12 下午11:05
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @title getTitle
     * @description
     * @createtime 2019/3/12 下午11:03
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * @title inline
     * @description set inline or not inline
     * @createtime 2019/2/27 下午12:22
     * @param \Closure $inline
     * @return \Closure|Inline
     */
    public function inline(\Closure $inline)
    {
        $inline = (new Inline($this->form, $inline));

        $this->assemblys[] = $inline;

        return $inline;
    }

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return string
     */
    public function render()
    {
        $render = "";
        foreach ($this->assemblys as $i => $v) {
            $render .= '<div class="layui-form-item">' . $v->render() . "</div>";
        }

        return $render;
    }
}