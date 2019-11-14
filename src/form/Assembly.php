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
     * 使用的attrs
     * @var array
     */
    protected $attrs = [];

    /**
     * 使用的class
     * @var array
     */
    protected $class = [];

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
    }

    /**
     * @title      setClass
     * @description 设置使用的class
     * @createtime 2019/11/14 6:11 下午
     * @param $class
     * @return $this
     * @author     yangyuance
     */
    public function setClass($class)
    {
        //防止传递空参数
        if(empty($class)){
            return $this;
        }

        if(is_array($class)){
            $class = join(" ", $class);
        }

        $this->class[] = $class;

        return $this;
    }

    /**
     * @title      getUseClass
     * @description 返回使用的class
     * @createtime 2019/11/14 5:45 下午
     * @return string
     * @author     yangyuance
     */
    public function getClass()
    {
        return join(" ", $this->class);
    }

    /**
     * @title      setAttrs
     * @description
     * @createtime 2019/11/14 11:58 下午
     * @param $attr
     * @return $this
     * @author     yangyuance
     */
    public function setAttrs($attr)
    {
        //防止传递空参数
        if(empty($attr)){
            return $this;
        }

        if(is_array($attr)){
            $attr = join(" ", $attr);
        }

        $this->attrs[] = $attr;

        return $this;
    }

    /**
     * @title      getAttrs
     * @description
     * @createtime 2019/11/14 11:58 下午
     * @return string
     * @author     yangyuance
     */
    public function getAttrs()
    {
        return join(" ", $this->attrs);
    }
}