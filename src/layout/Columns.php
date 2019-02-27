<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 下午2:56
 */

namespace Yirius\Admin\layout;


use think\Collection;
use Yirius\Admin\Layout;

class Columns extends Layout
{
    /**
     * width for each columns
     *
     * @var array
     */
    protected $width = [];

    /**
     * each columns html
     *
     * @var array
     */
    protected $htmls = [];

    /**
     * Columns constructor.
     * @param $html
     * @param int $width
     */
    public function __construct($html, $width = 12)
    {
        $this->setHtmls($html);

        $this->setWidth($width);
    }

    /**
     * @title setHtmls
     * @description set cloumns html content
     * @createtime 2019/1/30 下午3:06
     * @param mixed $html
     * @return Columns
     */
    public function setHtmls($html)
    {
        if ($html instanceof \Closure) {
            call_user_func($html, $this);
        } else {
            $this->htmls[] = $html;
        }

        return $this;
    }

    /**
     * @title setWidth
     * @description set columns width
     * @createtime 2019/1/30 下午3:08
     * @param int|array $width
     * @return Columns
     */
    public function setWidth($width = 12)
    {
        //if width is true empty
        if (is_null($width) || (is_array($width) && count($width) === 0)) {
            $this->width['md'] = 12;
        } elseif (is_numeric($width)) {
            //if set a num, then use it for md
            $this->width['md'] = $width;
        } else {
            //else all mean it is an array
            $this->width = $width;
        }

        return $this;
    }

    /**
     * @title addRow
     * @description add a row use Yirius\Admin\Rows
     * @createtime 2019/1/30 下午3:14
     * @param $html
     * @return Columns
     */
    public function rows($html)
    {
        if ($html instanceof \Closure) {
            $rows = new Rows();
            call_user_func($html, $rows);
        } else {
            $rows = new Rows($html);
        }

        $this->setHtmls($rows->render());

        return $this;
    }

    /**
     * @title render
     * @description use for render each type
     * @createtime 2019/1/30 下午3:10
     * @return string
     */
    public function render()
    {
        //collect each and join classname
        $class = join(" ", Collection::make($this->width)->each(function ($item, $key) {
            return "layui-col-" . $key . $item;
        })->toArray());

        //records render htmls
        $render = "";
        foreach ($this->htmls as $html) {
            if ($html instanceof Layout) {
                $render .= $html->render();
            } else {
                $render .= $html;
            }
        }

        return <<<HTML
<div class="{$class}">
{$render}
</div>
HTML;

    }
}