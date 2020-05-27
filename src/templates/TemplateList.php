<?php


namespace Yirius\Admin\templates;


use Yirius\Admin\templates\admin\AdminList;
use Yirius\Admin\templates\form\FormList;
use Yirius\Admin\templates\table\TableList;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class TemplateList
 * @package Yirius\Admin\templates
 * @method static TableList table();
 * @method static FormList form();
 * @method static AdminList admin();
 */
class TemplateList
{
    private static $classList = [
        "table" => TableList::class,
        "form" => FormList::class,
        "admin" => AdminList::class
    ];

    private static $instanceList = [];

    public static function __callStatic($name, $arguments)
    {
        $name = strtolower($name);
        if(isset(self::$classList[$name])) {
            if(!isset(self::$instanceList[$name])) {
                self::$instanceList[$name] = new self::$classList[$name]();
            }
            return self::$instanceList[$name];
        } else {
            ThinkerAdmin::Send()->json([], 0, "未找到对应TemplateList");
        }
    }
}