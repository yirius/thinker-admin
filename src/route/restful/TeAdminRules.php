<?php


namespace Yirius\Admin\route\restful;


use think\db\Query;
use Yirius\Admin\extend\ThinkerRestful;

class TeAdminRules extends ThinkerRestful
{
    protected $_UseTable = \Yirius\Admin\route\model\TeAdminRules::class;

}