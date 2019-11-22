<?php

namespace Yirius\Admin\form;

use Yirius\Admin\extend\ThinkerLayout;

/**
 * Class Assembly
 * @package Yirius\Admin\form
 * @method Assembly setText($text);
 * @method Assembly getText();
 *
 * @method Assembly setValue($value);
 * @method Assembly getValue();
 *
 * @method Assembly setField($field);
 * @method Assembly getField();
 */
abstract class Assembly extends ThinkerLayout
{
    /**
     * assembly's label text
     * @var string
     */
    protected $text = '';

    /**
     * assembly's value
     * @var string
     */
    protected $value = null;

    /**
     * @var array
     */
    protected $class = ['layui-input-block'];

    /**
     * @var null
     */
    protected $formIns = null;

    /**
     * Assembly constructor.
     * @param $field
     * @param $text
     */
    public function __construct($field = "", $text = "")
    {
        parent::__construct();

        //set assembly's config
        $this->setField($field)->setText($text)->setId("thinker_" . $field);

        $this->_init();
    }

    /**
     * @title      getLabel
     * @description 获取label的html
     * @createtime 2019/11/19 7:24 下午
     * @return string
     * @author     yangyuance
     */
    public function getLabel()
    {
        $text = $this->getText();

        if(empty($text)){
            $this->removeClass('layui-input-block')->setClass('layui-input-inline');

            return '';
        }else{
            return '<label class="layui-form-label">'.$text.'</label>';
        }
    }

    protected function _init(){

    }
}