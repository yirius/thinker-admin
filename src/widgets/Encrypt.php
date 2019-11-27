<?php


namespace Yirius\Admin\widgets;


class Encrypt extends Widgets
{
    /**
     * 公钥记录
     * @resource
     */
    protected $publicKey = null;

    /**
     * 私钥记录
     * @resource
     */
    protected $privateKey = null;

    protected $splitChar = "&";

    /**
     * @title      setSplitChar
     * @description 设置分隔符
     * @createtime 2019/11/27 5:42 下午
     * @param $splitChar
     * @return $this
     * @author     yangyuance
     */
    public function setSplitChar($splitChar)
    {
        $this->splitChar = $splitChar;

        return $this;
    }

    /**
     * @title      sortToString
     * @description 对一个数组进行排序,然后把它的值序列化
     * @createtime 2019/11/27 5:43 下午
     * @param array $data
     * @return string
     * @author     yangyuance
     */
    public function sortToString(array $data){
        ksort($data);
        $temp = [];
        foreach($data as $i => $v){
            if(isset($v)){
                if(is_array($v)){
                    $temp[] = $i . "=" . $this->sortToString($v);
                }else{
                    $temp[] = $i . "=" . $v;
                }
            }
        }
        return join($this->splitChar, $temp);
    }

    /**
     * @title      encryptMD5
     * @description 获取到md5加密之后的净值
     * @createtime 2019/11/27 5:44 下午
     * @param      $data
     * @param      $signKey
     * @param bool $isUpper
     * @return string
     * @author     yangyuance
     */
    public function encryptMD5($data, $signKey = "", $isUpper = true){
        if($isUpper){
            return strtoupper(md5($this->sortToString($data).$signKey));
        }else{
            return strtolower(md5($this->sortToString($data).$signKey));
        }
    }

    /**
     * @title      verfiyMD5
     * @description 验证MD5加密是否正确
     * @createtime 2019/11/27 5:46 下午
     * @param        $signString
     * @param array  $data
     * @param string $signKey
     * @return bool
     * @author     yangyuance
     */
    public function verfiyMD5($signString, array $data, $signKey = ""){
        return strtoupper($signString) == strtoupper(md5($this->sortToString($data).$signKey));
    }

    /**
     * @title      setPrivateKey
     * @description
     * @createtime 2019/11/27 5:49 下午
     * @param $privateKey
     * @return bool
     * @author     yangyuance
     */
    public function setPrivateKey($privateKey)
    {
        $privateKey = Tools::getInstance()->neatCertificate($privateKey);

        $resource = openssl_pkey_get_private($privateKey);

        if($resource){
            $this->privateKey = $resource;
            unset($publicKey);
            return true;
        }else{
            unset($publicKey);
            return false;
        }
    }

    /**
     * @title      setPublicKey
     * @description
     * @createtime 2019/11/27 5:47 下午
     * @param $publicKey
     * @return bool
     * @author     yangyuance
     */
    public function setPublicKey($publicKey)
    {
        $publicKey = Tools::getInstance()->neatCertificate($publicKey, false);

        $resource = openssl_pkey_get_public($publicKey);

        if($resource){
            $this->publicKey = $resource;
            unset($publicKey);
            return true;
        }else{
            unset($publicKey);
            return false;
        }
    }

    /**
     * @title      publicKeyEncrypt
     * @description 使用公钥进行加密
     * @createtime 2019/11/27 5:50 下午
     * @param string|array $string
     * @return bool|string
     * @author     yangyuance
     */
    public function publicKeyEncrypt($string)
    {
        if(empty($this->publicKey)){
            return false;
        }

        if(is_array($string)){
            $string = $this->sortToString($string);
        }

        $encryptData = '';
        foreach (str_split($string, 117) as $value){
            openssl_public_encrypt($value, $crypted, $this->publicKey);
            $encryptData .= $crypted;
        }

        return base64_encode($encryptData);
    }

    /**
     * @title      publicKeyDecrypt
     * @description 使用公钥验证解密
     * @createtime 2019/11/27 5:51 下午
     * @param $encryptKey
     * @return mixed
     * @author     yangyuance
     */
    public function publicKeyDecrypt($encryptKey)
    {
        if(empty($this->publicKey)){
            return false;
        }

        openssl_public_decrypt(base64_decode($encryptKey), $decrypted, $this->publicKey);
        return $decrypted;
    }

    /**
     * @title      privateKeyEncrypt
     * @description 使用私钥进行加密
     * @createtime 2019/11/27 5:56 下午
     * @param string|array $string
     * @param int $padding
     * @return bool|string
     * @author     yangyuance
     */
    public function privateKeyEncrypt($string, $padding = OPENSSL_PKCS1_PADDING)
    {
        if(empty($this->privateKey)){
            return false;
        }

        if(is_array($string)){
            $string = $this->sortToString($string);
        }

        openssl_private_encrypt($string, $sign, $this->privateKey, $padding);

        return base64_encode($sign);
    }

    /**
     * @title      publicKeyDecrypt
     * @description 使用公钥验证解密
     * @createtime 2019/11/27 5:51 下午
     * @param string $encryptKey
     * @return mixed
     * @author     yangyuance
     */
    public function privateKeyDecrypt($encryptKey)
    {
        if(empty($this->privateKey)){
            return false;
        }

        openssl_private_decrypt(base64_decode($encryptKey), $decrypted, $this->privateKey);

        return $decrypted;
    }

    /**
     * @title      privateKeySign
     * @description 使用私钥进行签名
     * @createtime 2019/11/27 5:57 下午
     * @param string|array $string
     * @param int $type
     * @return bool|string
     * @author     yangyuance
     */
    public function privateKeySign($string, $type = OPENSSL_ALGO_SHA1)
    {
        if(empty($this->privateKey)){
            return false;
        }

        if(is_array($string)){
            $string = $this->sortToString($string);
        }

        openssl_sign($string, $sign, $this->privateKey, $type);

        return base64_encode($sign);
    }

    /**
     * @title      publicKeyVerfiy
     * @description 使用公钥验证加密是否正确
     * @createtime 2019/11/27 5:52 下午
     * @param array|string $string
     * @param string $sign
     * @param int $type
     * @return bool
     * @author     yangyuance
     */
    public function publicKeyVerfiy($string, $sign, $type = OPENSSL_ALGO_SHA1)
    {
        if(empty($this->publicKey)){
            return false;
        }

        if(is_array($string)){
            $string = $this->sortToString($string);
        }

        return (bool)openssl_verify($string, base64_decode($sign), $this->publicKey, $type);
    }

    /**
     * @title      aesEncrypt
     * @description 用openssl对aes加密
     * @createtime 2019/11/27 5:59 下午
     * @param array|string $strSrc
     * @param        $aesKey
     * @param string $method
     * @return string
     * @author     yangyuance
     */
    public function aesEncrypt($strSrc, $aesKey, $method = "AES-128-ECB")
    {
        if(is_array($strSrc)){
            $strSrc = $this->sortToString($strSrc);
        }

        return openssl_encrypt($strSrc, $method, $aesKey);
    }

    /**
     * @title      aesDecrypt
     * @description 对3des加密进行解密
     * @createtime 2019/11/27 5:59 下午
     * @param string|array $strSrc
     * @param        $aesKey
     * @param string $method
     * @return string
     * @author     yangyuance
     */
    public function aesDecrypt($strSrc, $aesKey, $method = "AES-128-ECB")
    {
        if(is_array($strSrc)){
            $strSrc = $this->sortToString($strSrc);
        }

        return openssl_decrypt($strSrc, $method, $aesKey);
    }
}