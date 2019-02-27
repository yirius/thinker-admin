<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午8:43
 */

namespace Yirius\Admin\form\assembly;


class Checkbox extends Radio
{
    protected $inputType = "checkbox";

    /**
     * @title primary
     * @description
     * @createtime 2019/2/24 下午8:59
     * @return $this
     * @throws \Exception
     */
    public function primary()
    {
        $this->setAttributes('lay-skin', 'primary');

        return $this;
    }

    /**
     * @title getName
     * @description re write getName
     * @createtime 2019/2/24 下午8:59
     * @return string
     */
    public function getName()
    {
        return $this->name . '[]';
    }
}