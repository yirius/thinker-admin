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

    protected function _init(){

    }
}