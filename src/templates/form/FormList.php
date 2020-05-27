<?php


namespace Yirius\Admin\templates\form;


use Yirius\Admin\ThinkerAdmin;

/**
 * Class FormList
 * @package Yirius\Admin\templates\form
 * @method IconPickerJs IconPickerJs();
 * @method OnEventJs OnEventJs();
 * @method SubmitJs SubmitJs();
 * @method TreeJs TreeJs();
 */
class FormList
{
    private $classList = [
        "iconpickerjs" => IconPickerJs::class,
        "oneventjs" => OnEventJs::class,
        "submitjs" => SubmitJs::class,
        "treejs" => TreeJs::class,
    ];

    public function __call($name, $arguments)
    {
        $name = strtolower($name);
        if(isset($this->classList[$name])) {
            return (new $this->classList[$name]());
        } else {
            ThinkerAdmin::response()->msg("未找到对应FormList:" . $name)->fail();
        }
    }
}