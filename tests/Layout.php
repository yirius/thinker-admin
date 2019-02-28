<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 下午1:18
 */

namespace Yirius\Admin\Tests;


use Yirius\Admin\layout\Columns;

class Layout
{
    public function columns(){
        (new Columns("<div>111</div>"))->render();
    }
}