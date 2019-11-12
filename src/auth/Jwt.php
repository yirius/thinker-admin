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
        'type' => "HS256",
        'key' => "",
        'rsakey' => [
            'privatekey' => '',
            'publickey' => '',
        ],
        'notbefore' => 0,
        'expire' => 43200//0标识不过期
    ];

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
     * @title      encode
     * @description
     * @createtime 2019/11/12 11:27 下午
     * @param $encryptData
     * @return mixed
     * @author     yangyuance
     */
    public function encode($encryptData)
    {
        if (empty($this->config['key'])) {
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

        if ($this->config['expire']) {
            $payload['exp'] = $payload['iat'] + $this->config['expire'];
        }

        return \Firebase\JWT\JWT::encode($payload, $this->config['key'], $this->config['type']);
    }

    /**
     * @title decode
     * @description
     * @createtime 2019/2/22 上午11:57
     * @param $jwt
     * @param \Closure|null $errCall
     * @return mixed
     */
    public function decode($jwt, \Closure $errCall = null)
    {
        if (empty($this->config['key'])) {
            ThinkerAdmin::Send()->json([], 0, lang("ensure there is a key in thinkeradmin's config"));
        }

        if (is_null($errCall)) {
            try {
                $result = \Firebase\JWT\JWT::decode($jwt, $this->config['key']);
                return json_decode(json_encode($result->payload), true);
            } catch (ExpiredException $err) {
                Admin::tools()->jsonSend([], 1001, lang("authorization has expired"));
            } catch (SignatureInvalidException $err) {
                Admin::tools()->jsonSend([], 1001, lang("incorrect authorization"));
            } catch (BeforeValidException $err) {
                Admin::tools()->jsonSend([], 1001, lang("need to relogin"));
            } catch (\Exception $err) {
                Admin::tools()->jsonSend([], 1001, lang("need to relogin with some error"));
            }
        } else {
            try {
                $result = Jwt::decode($jwt, $this->config['key']);
                return json_decode(json_encode($result->payload), true);
            } catch (\Exception $err) {
                $errCall($err);
            }
        }
    }
}