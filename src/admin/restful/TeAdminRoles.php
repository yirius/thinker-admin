<?php


namespace Yirius\Admin\admin\restful;


use think\App;
use Yirius\Admin\admin\model\AdminGroupModel;
use Yirius\Admin\admin\model\AdminLogsModel;
use Yirius\Admin\extend\ThinkerRestful;

class TeAdminRoles extends ThinkerRestful
{
    protected $tokenAuth = false;

    public function __construct(App $app = null)
    {
        parent::__construct($app);

        $this->_UseTable = AdminGroupModel::class;

        $this->_Where = [
            "status",
            ["title", "like", "%_var%"]
        ];
    }
}