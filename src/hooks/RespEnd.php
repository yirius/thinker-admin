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
            $nohttp = config('thinkeradmin.log.nohttp');
            if(empty($nohttp)) $nohttp = [];

            //获取到当前访问的网址
            if(empty($_SERVER['__REQUESTURL'])){
                //不存在thinkerController的参数
                if(empty($_SERVER['PATH_INFO'])){
                    $requestUrl = "";
                }else{
                    $requestUrl = $_SERVER['PATH_INFO'];
                }
            }else{
                $requestUrl = $_SERVER['__REQUESTURL'];
            }

            if(!empty($requestUrl)){
                $requestUrl = strtolower(str_replace([".html",".html",".php"], "", $requestUrl));

                //取消不记录，需要记录
                $isHttp = false;
                //是否不记录
                $isNoHttp = false;
                for($i = 0; $i < count($nohttp); $i++){
                    $noHttpCode = $this->checkRule($requestUrl, $nohttp[$i]);
                    if($noHttpCode == 2){
                        $isHttp = true;
                    }else if($noHttpCode == 1){
                        $isNoHttp = true;
                    }
                }

                //只记录执行http记录的
                if(!$isNoHttp || $isHttp){
                    thinker_path_log(input('param.'));
                }
            }
        }
    }

    /**
     * @title      checkRule
     * @description
     * @createtime 2019/12/5 5:12 下午
     * @param $requestUrl
     * @param $noHttpRule
     * @return bool|int 1说明nohttp，2说明是取消规则，也就是记录
     * @author     yangyuance
     */
    protected function checkRule($requestUrl, $noHttpRule)
    {
        if(substr($noHttpRule, 0, 1) == "!"){
            //存在全部匹配下的取消，去掉取消符号后找到了规则，说明去需要记录http
            if($this->checkRule($requestUrl, substr($noHttpRule, 1))){
                //不记录http，相反，也就是记录
                return 2;
            }
        }else if(strpos($noHttpRule, "*") !== false){
            //存在全部匹配
            $noRule = str_replace("*", "", $noHttpRule);
            //如果不是全部
            if(strpos($requestUrl, $noRule) !== false){
                return 1;
            }
        }else{
            //就是直接匹配
            if($requestUrl == $noHttpRule){
                return 1;
            }
        }

        return false;
    }
}