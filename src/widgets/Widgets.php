<?php


namespace Yirius\Admin\widgets;


abstract class Widgets
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @title      setArguments
     * @description
     * @createtime 2019/11/13 11:00 下午
     * @param array $args
     * @author     yangyuance
     */
    public function setArguments(array $args){
        $this->args = $args;
    }
}