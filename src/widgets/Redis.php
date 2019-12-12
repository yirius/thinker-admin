<?php


namespace Yirius\Admin\widgets;


class Redis extends Widgets
{
    protected $config = [
        'expire'     => 60,
        'default'    => 'default',
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '',
        'select'     => 0,
        'timeout'    => 0,
    ];

    /**
     * @var Redis
     */
    protected $redisIns = null;

    /**
     * @title      _init
     * @description
     * @createtime 2019/12/12 10:32 下午
     * @author     yangyuance
     */
    public function _init()
    {
        $config = config("thinkeradmin.redis");

        if(is_array($config)){
            $this->config = array_merge($this->config, $config);

            unset($config);
        }

        //如果存在redis扩展
        if(extension_loaded("redis")){
            try{
                $this->redisIns = new \Redis();
                $this->redisIns->connect($this->config['host'], $this->config['port']);
                //如果存在密码
                if(!empty($this->config['password'])){
                    $this->redisIns->auth($this->config['password']);
                }
                $this->redisIns->select($this->config['select']);
            }catch (\Exception $exception){
                thinker_error($exception);
            }
        }
    }

    /**
     * @title      getKeys
     * @description
     * @createtime 2019/12/12 10:39 下午
     * @param string $field
     * @return array
     * @author     yangyuance
     */
    public function getKeys($field = "*")
    {
        if(!is_null($this->redisIns)){
            return $this->redisIns->keys("*");
        }else{
            return [];
        }
    }
}