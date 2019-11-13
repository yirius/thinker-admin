<?php


namespace Yirius\Admin\auth;


use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Yirius\Admin\ThinkerAdmin;

class Jwt
{
    /**
     * @var array
     */
    protected $config = [
        'isRsa' => false,
        'key' => "",
        'rsakey' => [
            'privatekey' => '',
            'publickey' => '',
        ],
        'notbefore' => 0,
        'expire' => 43200,//0标识不过期
        //过期jwt返回重新登录状态
        'expired_code' => 1001,
        //设置多长时间内没有auth验证操作就不在续期token
        'expired_operate_time' => 7200,
        //如果当前设定时间内存在验证Auth的操作，就返回重新设置token的状态
        'reexpired_code' => 1002
    ];

    /**
     * 过期错误
     * @var null
     */
    protected $expiredCall = null;

    /**
     * 签名无效错误
     * @var null
     */
    protected $signInvalidCall = null;

    /**
     * 提前使用错误
     * @var null
     */
    protected $beforeValidCall = null;

    /**
     * @var null 其他错误
     */
    protected $errorCall = null;

    /**
     * Adminjwt constructor.
     * @param array|null $config
     */
    function __construct(array $config = null)
    {
        if (is_null($config)) {
            $config = config("thinkeradmin.jwt");
        }

        if (is_array($config)) {
            $this->setConfig($config);
        }
    }

    /**
     * @title      setConfig
     * @description
     * @createtime 2019/11/12 11:26 下午
     * @param      $config
     * @param null $value
     * @return $this
     * @author     yangyuance
     */
    public function setConfig($config, $value = null)
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        } else {
            if (!is_null($value) && is_string($config)) {
                $this->config[$config] = $value;
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @title      getKeyAndAlgs
     * @description 获取到key的类型和算法类型
     * @createtime 2019/11/13 12:33 下午
     * @param bool $isEncode
     * @return array
     * @author     yangyuance
     */
    protected function getKeyAndAlgs($isEncode = true)
    {
        if($isEncode){
            return [
                $this->config['isRsa'] ? $this->config['rsakey']['privatekey'] : $this->config['key'],
                $this->config['isRsa'] ? "RS256" : "HS256"
            ];
        }else{
            return [
                $this->config['isRsa'] ? $this->config['rsakey']['publickey'] : $this->config['key'],
                [$this->config['isRsa'] ? "RS256" : "HS256"]
            ];
        }
    }

    /**
     * @title      setExpiredCall
     * @description
     * @createtime 2019/11/13 12:38 下午
     * @param \Closure $expiredCall
     * @return $this
     * @author     yangyuance
     */
    public function setExpiredCall(\Closure $expiredCall)
    {
        $this->expiredCall = $expiredCall;

        return $this;
    }

    /**
     * @title      setSignInvalidCall
     * @description
     * @createtime 2019/11/13 12:38 下午
     * @param \Closure $signInvalidCall
     * @return $this
     * @author     yangyuance
     */
    public function setSignInvalidCall(\Closure $signInvalidCall)
    {
        $this->signInvalidCall = $signInvalidCall;

        return $this;
    }

    /**
     * @title      setBeforeValidCall
     * @description
     * @createtime 2019/11/13 12:39 下午
     * @param \Closure $beforeValidCall
     * @return $this
     * @author     yangyuance
     */
    public function setBeforeValidCall(\Closure $beforeValidCall)
    {
        $this->beforeValidCall = $beforeValidCall;

        return $this;
    }

    /**
     * @title      setErrorCall
     * @description
     * @createtime 2019/11/13 12:39 下午
     * @param \Closure $errorCall
     * @return $this
     * @author     yangyuance
     */
    public function setErrorCall(\Closure $errorCall)
    {
        $this->errorCall = $errorCall;

        return $this;
    }

    /**
     * @title      encode
     * @description 对数据进行加密
     * @createtime 2019/11/12 11:27 下午
     * @param array $encryptData
     * @return mixed
     * @author     yangyuance
     */
    public function encode(array $encryptData)
    {
        list($key, $algType) = $this->getKeyAndAlgs();

        if (empty($key)) {
            ThinkerAdmin::Send()->json([], 0, lang("ensure there is a key in thinkeradmin's config"));
        }

        $payload = [
            'payload' => $encryptData
        ];

        /**
         * 设置创建时间
         */
        $payload['iat'] = time();

        /**
         * 如果存在notbefore,就设置
         */
        if ($this->config['notbefore']) {
            $payload['nbf'] = $payload['iat'] + $this->config['notbefore'];
        }

        /**
         * 设置过期时间
         */
        if ($this->config['expire']) {
            $payload['exp'] = $payload['iat'] + $this->config['expire'];
        }

        return \Firebase\JWT\JWT::encode($payload, $key, $algType);
    }

    /**
     * @title decode
     * @description
     * @createtime 2019/2/22 上午11:57
     * @param $jwt
     * @return mixed
     */
    public function decode($jwt)
    {
        list($key, $algType) = $this->getKeyAndAlgs(false);

        if (empty($key)) {
            ThinkerAdmin::Send()->json([], 0, lang("ensure there is a key in thinkeradmin's config"));
        }

        try {
            $result = \Firebase\JWT\JWT::decode($jwt, $key, $algType);
            return json_decode(json_encode($result->payload), true);
        } catch (ExpiredException $err) {
            if($this->expiredCall instanceof \Closure){
                //过期是肯定可以解出来数据的
                $bodyb64 = explode('.', $jwt)[1];
                $payload = \Firebase\JWT\JWT::jsonDecode(\Firebase\JWT\JWT::urlsafeB64Decode($bodyb64));
                //将用户数据传递给过期回调
                call_user_func($this->expiredCall, $payload, $err);
            }else{
                ThinkerAdmin::Send()->json([], $this->config['expired_code'], lang("authorization has expired"));
            }
        } catch (SignatureInvalidException $err) {
            if($this->signInvalidCall instanceof \Closure){
                call_user_func($this->signInvalidCall, null, $err);
            }else{
                ThinkerAdmin::Send()->json([], $this->config['expired_code'], lang("incorrect authorization"));
            }
        } catch (BeforeValidException $err) {
            if($this->beforeValidCall instanceof \Closure){
                call_user_func($this->beforeValidCall, null, $err);
            }else{
                ThinkerAdmin::Send()->json([], $this->config['expired_code'], lang("need to relogin"));
            }
        } catch (\Exception $err) {
            if($this->errorCall instanceof \Closure){
                call_user_func($this->errorCall, null, $err);
            }else{
                ThinkerAdmin::Send()->json([], $this->config['expired_code'], lang("need to relogin with some error"));
            }
        }
    }
}