<?php


namespace Yirius\Admin\services;


use Redis;
use think\facade\Cache;
use Yirius\Admin\ThinkerAdmin;

class RedisService
{
    /**
     * @var Redis
     */
    private $redis;

    public function __construct()
    {
        $this->redis = Cache::store("redis");

        if(empty($this->redis)) {
            ThinkerAdmin::response()->msg("当前非Redis连接，无法使用RedisService")->fail();
        }
    }

    /**
     * @return Redis
     */
    public function getRedis()
    {
        return $this->redis->handler();
    }
}