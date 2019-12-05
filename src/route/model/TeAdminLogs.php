<?php


namespace Yirius\Admin\route\model;


use think\facade\Request;
use Yirius\Admin\extend\ThinkerModel;

class TeAdminLogs extends ThinkerModel
{
    protected $table = "teadmin_logs";

    /**
     * @title      addLog
     * @description
     * @createtime 2019/11/28 6:24 下午
     * @param array $tokenInfo
     * @param       $desc
     * @param bool  $isLogin
     * @author     yangyuance
     */
    public static function addLog(array $tokenInfo, $desc, $isLogin = false)
    {
        $useTime = ceil((microtime(true) - app()->getBeginTime())*10000);

        self::insert([
            'userid' => isset($tokenInfo['id']) ? $tokenInfo['id'] : 0,
            'user_type' => isset($tokenInfo['access_type']) ? $tokenInfo['access_type'] : 0,
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

        $useCall = "未知函数";
        for($i = 2; $i < count($data); $i++){
            if(isset($data[$i]['class']) && isset($data[$i]['function'])){
                $useCall = $data[$i]['class']."::".$data[$i]['function'];
                break;
            }
        }

        unset($data);

        return $useCall;
    }
}