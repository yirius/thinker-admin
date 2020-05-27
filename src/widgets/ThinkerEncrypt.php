<?php


namespace Yirius\Admin\widgets;


use Yirius\Admin\ThinkerAdmin;

class ThinkerEncrypt
{
    /**
     * @title      md5
     * @description 进行md5大写加密
     * @createtime 2020/5/27 12:19 下午
     * @param $str
     * @return string
     * @author     yangyuance
     */
    public function md5($str) {
        return strtoupper(md5($str));
    }

    /**
     * @title      desEncrypt
     * @description
     * @createtime 2020/5/27 12:22 下午
     * @param $str
     * @param $key
     * @return string
     * @author     yangyuance
     */
    public function desEncrypt($str, $key) {
        return openssl_encrypt($str, "DES-EDE-CBC", $key, 0, "01234567");
    }

    /**
     * @title      desDecrypt
     * @description
     * @createtime 2020/5/27 12:22 下午
     * @param $str
     * @param $key
     * @return string
     * @author     yangyuance
     */
    public function desDecrypt($str, $key) {
        return openssl_decrypt($str, "DES-EDE-CBC", $key, 0, "01234567");
    }

    /**
     * @title      rsaSign
     * @description
     * @createtime 2020/5/27 12:28 下午
     * @param $str
     * @param $privateKey
     * @param $type
     * @return string
     * @author     yangyuance
     */
    public function rsaSign($str, $privateKey, $type) {
        if(is_string($privateKey)) {
            $privateKey = openssl_pkey_get_private(ThinkerAdmin::tools()->neatCertificate($privateKey));
        }

        openssl_sign($str, $sign, $privateKey, $type);

        return base64_encode($sign);
    }

    /**
     * @title      rsaMd5Sign
     * @description
     * @createtime 2020/5/27 12:29 下午
     * @param $str
     * @param $privateKey
     * @return string
     * @author     yangyuance
     */
    public function rsaMd5Sign($str, $privateKey) {
        return $this->rsaSign($str, $privateKey, OPENSSL_ALGO_MD5);
    }

    /**
     * @title      rsaSha1Sign
     * @description
     * @createtime 2020/5/27 12:33 下午
     * @param $str
     * @param $privateKey
     * @return string
     * @author     yangyuance
     */
    public function rsaSha1Sign($str, $privateKey) {
        return $this->rsaSign($str, $privateKey, OPENSSL_ALGO_SHA1);
    }

    /**
     * @title      rsaSha256Sign
     * @description
     * @createtime 2020/5/27 12:34 下午
     * @param $str
     * @param $privateKey
     * @return string
     * @author     yangyuance
     */
    public function rsaSha256Sign($str, $privateKey) {
        return $this->rsaSign($str, $privateKey, OPENSSL_ALGO_SHA256);
    }

    /**
     * @title      rsaSha512Sign
     * @description
     * @createtime 2020/5/27 12:34 下午
     * @param $str
     * @param $privateKey
     * @return string
     * @author     yangyuance
     */
    public function rsaSha512Sign($str, $privateKey) {
        return $this->rsaSign($str, $privateKey, OPENSSL_ALGO_SHA512);
    }

    /**
     * @title      rsaVerify
     * @description
     * @createtime 2020/5/27 12:32 下午
     * @param $data
     * @param $signStr
     * @param $publicKey
     * @param $type
     * @return bool
     * @author     yangyuance
     */
    public function rsaVerify($data, $signStr, $publicKey, $type) {
        if(is_string($publicKey)) {
            $publicKey = openssl_pkey_get_public(ThinkerAdmin::tools()->neatCertificate($publicKey, false));
        }

        return (bool) openssl_verify($data, base64_decode($signStr), $publicKey, $type);
    }

    /**
     * @title      rsaMd5Verify
     * @description
     * @createtime 2020/5/27 12:33 下午
     * @param $data
     * @param $signStr
     * @param $publicKey
     * @return bool
     * @author     yangyuance
     */
    public function rsaMd5Verify($data, $signStr, $publicKey) {
        return $this->rsaVerify($data, $signStr, $publicKey, OPENSSL_ALGO_MD5);
    }

    /**
     * @title      rsaSha1Verify
     * @description
     * @createtime 2020/5/27 12:33 下午
     * @param $data
     * @param $signStr
     * @param $publicKey
     * @return bool
     * @author     yangyuance
     */
    public function rsaSha1Verify($data, $signStr, $publicKey) {
        return $this->rsaVerify($data, $signStr, $publicKey, OPENSSL_ALGO_SHA1);
    }

    /**
     * @title      rsaSha256Verify
     * @description
     * @createtime 2020/5/27 12:33 下午
     * @param $data
     * @param $signStr
     * @param $publicKey
     * @return bool
     * @author     yangyuance
     */
    public function rsaSha256Verify($data, $signStr, $publicKey) {
        return $this->rsaVerify($data, $signStr, $publicKey, OPENSSL_ALGO_SHA256);
    }

    /**
     * @title      rsaSha512Verify
     * @description
     * @createtime 2020/5/27 12:33 下午
     * @param $data
     * @param $signStr
     * @param $publicKey
     * @return bool
     * @author     yangyuance
     */
    public function rsaSha512Verify($data, $signStr, $publicKey) {
        return $this->rsaVerify($data, $signStr, $publicKey, OPENSSL_ALGO_SHA512);
    }


    /**
     * @title      publicKeyEncrypt
     * @description 使用公钥进行加密
     * @createtime 2019/11/27 5:50 下午
     * @param string|array $string
     * @return bool|string
     * @author     yangyuance
     */
    public function rsaEncrypt($string, $publicKey){
        if(is_string($publicKey)) {
            $publicKey = openssl_pkey_get_public(
                ThinkerAdmin::tools()->neatCertificate($publicKey, false)
            );
        }

        $encryptData = '';
        foreach (str_split($string, 117) as $value){
            openssl_public_encrypt($value, $crypted, $publicKey);
            $encryptData .= $crypted;
        }

        return base64_encode($encryptData);
    }

    /**
     * @title      rsaDecrypt
     * @description
     * @createtime 2020/5/27 12:40 下午
     * @param $string
     * @param $publicKey
     * @return string
     * @author     yangyuance
     */
    public function rsaDecrypt($string, $publicKey) {
        if(is_string($publicKey)) {
            $publicKey = openssl_pkey_get_public(
                ThinkerAdmin::tools()->neatCertificate($publicKey, false)
            );
        }

        openssl_public_decrypt(base64_decode($string), $decrypted, $publicKey);

        return $decrypted;
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
        return openssl_decrypt($strSrc, $method, $aesKey);
    }

    /**
     * @title      sortArray
     * @description 对一个数组进行排序,然后把它的值序列化
     * @createtime 2020/5/27 12:42 下午
     * @param array  $data
     * @param string $joinChar
     * @return string
     * @author     yangyuance
     */
    public function sortArray(array $data, $joinChar = "&"){
        ksort($data);
        $temp = [];
        foreach($data as $i => $v){
            if(isset($v)){
                if(is_array($v)){
                    $temp[] = $i . "=" . $this->sortArray($v, $joinChar);
                }else{
                    $temp[] = $i . "=" . $v;
                }
            }
        }
        return join($joinChar, $temp);
    }
}