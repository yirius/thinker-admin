<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/9
 * Time: 下午11:17
 */

namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\form\Assembly;

class Html extends Assembly
{
    protected $plain = false;

    /**
     * @title plain
     * @description
     * @createtime 2019/3/9 下午11:19
     * @return $this
     */
    public function plain()
    {
        $this->plain = true;

        return $this;
    }

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        if($this->plain){
            return $this->getValue();
        }

        return <<<HTML
{$this->getLabel()}
<div class="{$this->getClass()}">
    {$this->getValue()}
</div>
HTML;
    }
}