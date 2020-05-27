<?php


namespace Yirius\Admin\templates\table\columns;


use Yirius\Admin\ThinkerAdmin;

/**
 * Class ColumnsList
 * @package Yirius\Admin\templates\table\columns
 * @method SwitchsJs SwitchsJs();
 * @method SwitchsTpl SwitchsTpl();
 */
class ColumnsList
{
    private $classList = [
        "switchsjs" => SwitchsJs::class,
        "switchstpl" => SwitchsTpl::class,
    ];

    public function __call($name, $arguments)
    {
        $name = strtolower($name);
        if(isset($this->classList[$name])) {
            return (new $this->classList[$name]());
        } else {
            ThinkerAdmin::Send()->json([], 0, "未找到对应ColumnsList");
        }
    }
}