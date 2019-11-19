<?php


namespace Yirius\Admin\widgets;


class Cache extends Widgets
{
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
        return \think\facade\Cache::tag("thinker_tokeninfo")->set(
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
        return \think\facade\Cache::get(
            $this->getTokenCacheName($prevName, $tokenInfo),
            $default
        );
    }

    /**
     * @title      getTokenCacheName
     * @description 获取Token相关缓存的名称
     * @createtime 2019/11/13 11:34 下午
     * @param $prevName
     * @param $tokenInfo
     * @return string
     * @author     yangyuance
     */
    public function getTokenCacheName($prevName, $tokenInfo)
    {
        return "thinker_tokeninfo_".$prevName."_".$tokenInfo['id']."_".$tokenInfo['access_type'];
    }

    /**
     * @title      clearTokenCache
     * @description 清空Token相关缓存
     * @createtime 2019/11/13 11:31 下午
     * @author     yangyuance
     */
    public function clearTokenCache()
    {
        return \think\facade\Cache::clear("thinker_tokeninfo");
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
        return \think\facade\Cache::tag("thinker_authinfo")->set(
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
        return \think\facade\Cache::get(
            $this->getAuthCacheName($prevName, $tokenInfo),
            $default
        );
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
        if(isset($tokenInfo['type'])){
            if(is_array($tokenInfo['type'])){
                $tokenInfo['type'] = join("_", $tokenInfo['type']);
            }
            return "thinker_authinfo_".$prevName."_".$tokenInfo['id']."_".$tokenInfo['access_type']."_".$tokenInfo['type'];
        }

        return "thinker_authinfo_".$prevName."_".$tokenInfo['id']."_".$tokenInfo['access_type'];
    }

    /**
     * @title      clearTokenCache
     * @description 清空Token相关缓存
     * @createtime 2019/11/13 11:31 下午
     * @author     yangyuance
     */
    public function clearAuthCache()
    {
        return \think\facade\Cache::clear("thinker_authinfo");
    }
}