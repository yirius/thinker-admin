<?php
/**
 * User: Yirius
 * Date: 2018/6/28
 * Time: 00:30
 */

namespace Yirius\Admin\extend;


use icesjwt\BeforeValidException;
use icesjwt\ExpiredException;
use icesjwt\Jwt;
use icesjwt\SignatureException;
use traits\controller\Jump;
use Yirius\Admin\Admin;

class Adminjwt
{
    use Jump;

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

    protected $algsCanUse = [
        'HS256' => ['hash_hmac', 'SHA256'],
        'HS512' => ['hash_hmac', 'SHA512'],
        'HS384' => ['hash_hmac', 'SHA384'],
        'RS256' => ['openssl', 'SHA256'],
        'RS384' => ['openssl', 'SHA384'],
        'RS512' => ['openssl', 'SHA512']
    ];

    protected static $instance = null;

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
     * @title setConfig
     * @description set jwt config
     * @createtime 2019/2/22 上午11:51
     * @param $config
     * @param null $value
     * @return $this
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
     * @title encode
     * @description encode data to base64 string
     * @createtime 2019/2/22 上午11:57
     * @param $encryptData
     * @return string
     */
    public function encode($encryptData)
    {

        if (empty($this->config['key'])) {
            throw new \Exception(lang("ensure there is a key in thinkeradmin's config"));
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
        return Jwt::encode($payload, $this->config['key'], $this->config['type']);
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
            throw new \Exception(lang("ensure there is a key in thinkeradmin's config"));
        }

        if (is_null($errCall)) {
            try {
                $result = Jwt::decode($jwt, $this->config['key']);
                return json_decode(json_encode($result->payload), true);
            } catch (ExpiredException $err) {
                Admin::tools()->jsonSend([], 1001, lang("authorization has expired"));
            } catch (SignatureException $err) {
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
