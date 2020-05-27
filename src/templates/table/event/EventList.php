<?php


namespace Yirius\Admin\templates\table\event;

use Yirius\Admin\ThinkerAdmin;

/**
 * Class EventList
 * @package Yirius\Admin\templates\table\event
 * @method ColsEventExpendJs ColsEventExpendJs();
 * @method GetCheckIdsJs GetCheckIdsJs();
 * @method PopupJs PopupJs();
 * @method ToolbarErrorRequestJs ToolbarErrorRequestJs();
 * @method ToolbarInputChange ToolbarInputChange();
 * @method ToolbarSubmitXlsxJs ToolbarSubmitXlsxJs();
 * @method VerifyJs VerifyJs();
 * @method MultiVerifyJs MultiVerifyJs();
 */
class EventList
{
    private $classList = [
        "colseventexpendjs" => ColsEventExpendJs::class,
        "getcheckidsjs" => GetCheckIdsJs::class,
        "multiverifyjs" => MultiVerifyJs::class,
        "popupjs" => PopupJs::class,
        "toolbarerrorrequestjs" => ToolbarErrorRequestJs::class,
        "toolbarinputchange" => ToolbarInputChange::class,
        "toolbarsubmitxlsxjs" => ToolbarSubmitXlsxJs::class,
        "verifyjs" => VerifyJs::class
    ];

    public function __call($name, $arguments)
    {
        $name = strtolower($name);
        if(isset($this->classList[$name])) {
            return (new $this->classList[$name]());
        } else {
            ThinkerAdmin::Send()->json([], 0, "未找到对应EventList");
        }
    }
}