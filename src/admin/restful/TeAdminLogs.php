<?php


namespace Yirius\Admin\admin\restful;


use think\App;
use Yirius\Admin\admin\model\AdminLogsModel;
use Yirius\Admin\extend\ThinkerRestful;

class TeAdminLogs extends ThinkerRestful
{
    protected $tokenAuth = false;

    public function __construct(App $app = null)
    {
        parent::__construct($app);

        $this->_UseTable = AdminLogsModel::class;
        $this->_Where = [
            "islogin", "userid", "requesttype",
            "create_time" => function($value){
                return ['create_time', "between", explode(" / ", $value)];
            }
        ];
    }
}