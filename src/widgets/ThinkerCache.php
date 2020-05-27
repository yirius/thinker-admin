<?php


namespace Yirius\Admin\widgets;


use think\facade\Cache;
use Yirius\Admin\config\ConsConfig;
use Yirius\Admin\ThinkerAdmin;

class ThinkerCache
{
    /**
     * @title      getTokenStr
     * @description 获取到token相关的str
     * @createtime 2020/5/26 11:49 下午
     * @param array $tokenInfo
     * @return string
     * @author     yangyuance
     */
    public function getTokenStr(array $tokenInfo) {
        return $tokenInfo[ConsConfig::$JWT_KEY]."_".$tokenInfo[ConsConfig::$JWT_ACCESS_TYPE];
    }

    /**
     * @title      setTokenOperateTime
     * @description 设置token操作时间，便于查看在线状态
     * @createtime 2020/5/26 11:50 下午
     * @param array $tokenInfo
     * @return bool|int
     * @author     yangyuance
     */
    public function setTokenOperateTime(array $tokenInfo) {
        return ThinkerAdmin::redis()->getRedis()->hSet("ADMIN_ONLINE", $this->getTokenStr($tokenInfo), date("Y-m-d H:i:s"));
    }

    /**
     * @title      getTokenOperateTime
     * @description 获取到token操作时间
     * @createtime 2020/5/26 11:51 下午
     * @param array $tokenInfo
     * @return string
     * @author     yangyuance
     */
    public function getTokenOperateTime(array $tokenInfo) {
        return ThinkerAdmin::redis()->getRedis()->hGet("ADMIN_ONLINE", $this->getTokenStr($tokenInfo));
    }

    /**
     * @title      getTokenCacheName
     * @description 获取到Token对应的缓存名称
     * @createtime 2020/5/26 11:52 下午
     * @param       $prevName
     * @param array $tokenInfo
     * @return string
     * @author     yangyuance
     */
    public function getTokenCacheName($prevName, array $tokenInfo) {
        return "thinker_tokeninfo_".$prevName."_".$this->getTokenStr($tokenInfo);
    }

    /**
     * @title      setTokenCache
     * @description 设置和token相关的缓存
     * @createtime 2019/11/13 11:32 下午
     * @param     $prevName
     * @param     $tokenInfo
     * @param     $value
     * @param int $expired
     * @return mixed
     * @author     yangyuance
     */
    public function setTokenCache($prevName, $tokenInfo, $value, $expired = 0)
    {
        return Cache::tag("thinker_tokeninfo")->set(
            $this->getTokenCacheName($prevName, $tokenInfo),
            $value,
            $expired
        );
    }

    /**
     * @title      getTokenCache
     * @description 获取跟Token相关的缓存内容
     * @createtime 2019/11/13 11:33 下午
     * @param $prevName
     * @param $tokenInfo
     * @param $default
     * @return string
     * @author     yangyuance
     */
    public function getTokenCache($prevName, $tokenInfo, $default = null)
    {
        return Cache::get(
            $this->getTokenCacheName($prevName, $tokenInfo),
            $default
        );
    }

    /**
     * @title      clearTokenCache
     * @description 清空Token相关缓存
     * @createtime 2019/11/13 11:31 下午
     * @author     yangyuance
     */
    public function clearTokenCache()
    {
        return Cache::clear("thinker_tokeninfo");
    }

    /**
     * @title      getAuthCacheName
     * @description 获取Auth相关缓存的名称
     * @createtime 2019/11/13 11:34 下午
     * @param $prevName
     * @param $tokenInfo
     * @return string
     * @author     yangyuance
     */
    public function getAuthCacheName($prevName, $tokenInfo)
    {
        $typeStr = "";
        if(isset($tokenInfo['type'])){
            $typeStr = "_" . $tokenInfo['type'];
        }

        return "thinker_authinfo_".$prevName."_".$this->getTokenStr($tokenInfo).$typeStr;
    }

    /**
     * @title      setTokenCache
     * @description 设置和token相关的缓存
     * @createtime 2019/11/13 11:32 下午
     * @param     $prevName
     * @param     $tokenInfo
     * @param     $value
     * @param int $expired
     * @return mixed
     * @author     yangyuance
     */
    public function setAuthCache($prevName, $tokenInfo, $value, $expired = 0)
    {
        return Cache::tag("thinker_authinfo")->set(
            $this->getAuthCacheName($prevName, $tokenInfo),
            $value,
            $expired
        );
    }

    /**
     * @title      getAuthCache
     * @description 获取跟Auth相关的缓存内容
     * @createtime 2019/11/16 6:33 下午
     * @param      $prevName
     * @param      $tokenInfo
     * @param null $default
     * @return mixed
     * @author     yangyuance
     */
    public function getAuthCache($prevName, $tokenInfo, $default = null)
    {
        return Cache::get(
            $this->getAuthCacheName($prevName, $tokenInfo),
            $default
        );
    }

    /**
     * @title      clearTokenCache
     * @description 清空Token相关缓存
     * @createtime 2019/11/13 11:31 下午
     * @author     yangyuance
     */
    public function clearAuthCache()
    {
        return Cache::clear("thinker_authinfo");
    }

    /**
     * @title      setTokenIp
     * @description 设置token操作时间，便于查看在线状态
     * @createtime 2020/5/26 11:57 下午
     * @param $tokenInfo
     * @param $ip
     * @return bool|int
     * @author     yangyuance
     */
    public function setTokenIp($tokenInfo, $ip) {
        return ThinkerAdmin::redis()->getRedis()->hSet("ADMIN_IP", $this->getTokenStr($tokenInfo), $ip);
    }

    /**
     * @title      getTokenIp
     * @description 设置Token对应IP
     * @createtime 2020/5/26 11:58 下午
     * @param $tokenInfo
     * @return string
     * @author     yangyuance
     */
    public function getTokenIp($tokenInfo) {
        return ThinkerAdmin::redis()->getRedis()->hGet("ADMIN_IP", $this->getTokenStr($tokenInfo));
    }
}