<?php


namespace Yirius\Admin\hooks;


class AppInit
{
    public function run($params)
    {
        $_SERVER['__STARTTIME'] = microtime(true);
    }
}