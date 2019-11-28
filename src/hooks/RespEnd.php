<?php


namespace Yirius\Admin\hooks;


use think\facade\Log;

class RespEnd
{
    /**
     * @title      run
     * @description 输出的时候记录一下日志
     * @createtime 2019/11/28 5:56 下午
     * @param $params
     * @author     yangyuance
     */
    public function run($params)
    {
        if(config('thinkeradmin.log.http')){
            $logConfig = config("log.");
            $logConfig['path'] = app()->getRuntimePath() . 'http' . DIRECTORY_SEPARATOR;

            $useTime = ceil((microtime(true) - $_SERVER['__STARTTIME'])*10000);

            try{
                Log::init($logConfig)->write(json_encode([
                    'usetime' => $useTime,
                    'usememory' => ceil(memory_get_usage()/1024/1024),
                    'params' => input('param.')
                ]), "info", true);
            }catch (\Exception $exception){

            }
            $logConfig['path'] = "";
            Log::init($logConfig);
        }
    }
}