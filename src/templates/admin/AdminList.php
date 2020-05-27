<?php


namespace Yirius\Admin\templates\admin;


use Yirius\Admin\ThinkerAdmin;

/**
 * Class AdminList
 * @package Yirius\Admin\templates\admin
 * @method RedisInfo RedisInfo();
 * @method RuleButtonJs RuleButtonJs();
 * @method RuleTreeJs RuleTreeJs();
 */
class AdminList
{
    private $classList = [
        "redisinfo" => RedisInfo::class,
        "rulebuttonjs" => RuleButtonJs::class,
        "ruletreejs" => RuleTreeJs::class,
    ];

    public function __call($name, $arguments)
    {
        $name = strtolower($name);
        if(isset($this->classList[$name])) {
            return (new $this->classList[$name]());
        } else {
            ThinkerAdmin::Send()->json([], 0, "未找到对应AdminList");
        }
    }
}