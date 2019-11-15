<?php


namespace Yirius\Admin\layout;


use Yirius\Admin\extend\ThinkerLayout;

trait setLayout
{
    /**
     * @var array
     */
    protected $layouts = [];

    /**
     * @title      layout
     * @description
     * @createtime 2019/11/15 2:54 下午
     * @param      $layout
     * @param null $field
     * @return setLayout
     * @author     yangyuance
     */
    public function layout($layout, $field = null)
    {
        return $this->setLayouts($layout, $field);
    }

    /**
     * @title      setLayouts
     * @description
     * @createtime 2019/11/15 2:54 下午
     * @param      $layout
     * @param null $field
     * @return $this
     * @author     yangyuance
     */
    public function setLayouts($layout, $field = null)
    {
        if(is_null($field)){
            $this->layouts[] = $layout;
        }else{
            if(!isset($this->layouts[$field])) $this->layouts[$field] = [];
            $this->layouts[$field][] = $layout;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getLayouts()
    {
        return $this->layouts;
    }
}