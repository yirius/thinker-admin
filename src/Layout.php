<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午4:11
 */

namespace Yirius\Admin;


abstract class Layout
{
    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public abstract function render();

    /**
     * @title __toString
     * @description
     * @createtime 2019/2/24 下午4:45
     * @return mixed
     */
    public function __toString()
    {
        return $this->render();
    }
}