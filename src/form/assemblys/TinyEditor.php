<?php


namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\ThinkerAdmin;

class TinyEditor extends Textarea
{
    /**
     * @title      _init
     * @description
     * @createtime 2019/11/16 11:13 下午
     * @author     yangyuance
     */
    protected function _init()
    {
        ThinkerAdmin::script("tinymce", false, true);

        $this->setAttrs('lay-tinymce', '');
    }
}