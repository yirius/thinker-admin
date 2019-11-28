<?php


namespace Yirius\Admin\route\restful;


use think\db\Query;
use Yirius\Admin\extend\ThinkerRestful;

class TeAdminLogs extends ThinkerRestful
{
    protected $_UseTable = \Yirius\Admin\route\model\TeAdminLogs::class;

    protected $_Where = [
        "islogin"
    ];
}