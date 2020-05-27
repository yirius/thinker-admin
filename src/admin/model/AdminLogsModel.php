<?php


namespace Yirius\Admin\admin\model;


use think\facade\Request;
use Yirius\Admin\config\ConsConfig;
use Yirius\Admin\extend\ThinkerModel;

class AdminLogsModel extends ThinkerModel
{
    protected $table = "teadmin_logs";

    public function addLog(array $tokenInfo, $desc, $isLogin = false) {
        $useTime = ceil((microtime(true) - app()->getBeginTime())*10000);

        self::insert([
            'userid' => isset($tokenInfo[ConsConfig::$JWT_KEY]) ? $tokenInfo[ConsConfig::$JWT_KEY] : 0,
            'user_type' => isset($tokenInfo[ConsConfig::$JWT_ACCESS_TYPE]) ? $tokenInfo[ConsConfig::$JWT_ACCESS_TYPE] : 0,
            'realname' => isset($tokenInfo['username']) ? $tokenInfo['username'] : json_encode($tokenInfo),
            'desc' => $desc,
            'usetime' => $useTime,
            'funcname' => self::getParentCall(),
            'requesttype' => Request::method(),
            'params' => json_encode(input('param.')),
            'ip' => Request::ip(),
            'address' => "",
            'islogin' => $isLogin ? 1 : 0,
            'create_time' => date("Y-m-d H:i:s"),
            'update_time' => date("Y-m-d H:i:s"),
        ]);
    }


    /**
     * @title      getParentCall
     * @description 获取到上级是哪个函数调用了
     * @createtime 2019/11/28 6:29 下午
     * @return string
     * @author     yangyuance
     */
    protected static function getParentCall()
    {
        $data = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $useCall = [];
        for($i = 2; $i < count($data); $i++){
            if($i > 11) {
                break;
            }
            if(isset($data[$i]['class']) && isset($data[$i]['function'])){
                $useCall[] = $data[$i]['class']."::".$data[$i]['function'];
            }
        }

        unset($data);

        return join(",", $useCall);
    }
}