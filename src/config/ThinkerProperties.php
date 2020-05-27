<?php


namespace Yirius\Admin\config;


class ThinkerProperties
{
    protected $config = [
        //log记录的一些参数
        'log' => [
            //是否记录每一次http请求
            'http' => true,
            //不记录堆栈的执行
            'nostack' => ['info'],
            //不记录http请求的前缀
            'nohttp' => [
                '/favicon.ico',
                //系统访问不记录
                '/thinkeradmin/*',
                '/restful/thinkeradmin/*',
            ]
        ],
        'shiro' => [
            //是否打开验证码
            'vercode' => true,
            'authTable' => [
                "\\Yirius\\Admin\\admin\\login\\AdminLogin"
            ],
            'permsUrl' => [

            ]
        ],
        //Json Web Token参数
        'jwt' => [
            //秘钥
            'secretKey' => "131313131231312131",
            //过期时间
            'tokenExpireSeconds' => 86400,//0标识不过期
            //过期jwt返回重新登录状态
            'expiredCode' => 1001,
            //设置多长时间内没有auth验证操作就不在续期token
            'expiredOperateTime' => 7200,
            //如果当前设定时间内存在验证Auth的操作，就返回重新设置token的状态
            'reExpiredCode' => 1002,
            //是否开启单点登录
            'singleLogin' => true
        ],
        //验证码信息
        'captcha' => [
            // 验证码字体大小
            'fontSize'    =>    30,
            // 验证码位数
            'length'      =>    4,
            // 关闭验证码杂点
            'useNoise'    =>    true,
            'height'      =>    38
        ]
    ];

    public function __construct()
    {
        $config = config("thinkeradmin.");

        if(!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * @title      getLog
     * @description
     * @createtime 2020/5/27 8:34 下午
     * @param null $key
     * @return mixed
     * @author     yangyuance
     */
    public function getLog($key = null) {
        if(!empty($key) && isset($this->config['log'][$key])) {
            return $this->config['log'][$key];
        }
        return $this->config['log'];
    }

    /**
     * @title      getShiro
     * @description
     * @createtime 2020/5/27 8:35 下午
     * @param null $key
     * @return mixed
     * @author     yangyuance
     */
    public function getShiro($key = null) {
        if(!empty($key) && isset($this->config['shiro'][$key])) {
            return $this->config['shiro'][$key];
        }
        return $this->config['shiro'];
    }

    /**
     * @title      getJwt
     * @description
     * @createtime 2020/5/27 8:35 下午
     * @param null $key
     * @return mixed
     * @author     yangyuance
     */
    public function getJwt($key = null) {
        if(!empty($key) && isset($this->config['jwt'][$key])) {
            return $this->config['jwt'][$key];
        }
        return $this->config['jwt'];
    }

    /**
     * @title      getCaptcha
     * @description
     * @createtime 2020/5/28 12:26 上午
     * @param null $key
     * @return mixed
     * @author     yangyuance
     */
    public function getCaptcha($key = null) {
        if(!empty($key) && isset($this->config['captcha'][$key])) {
            return $this->config['captcha'][$key];
        }
        return $this->config['captcha'];
    }
}