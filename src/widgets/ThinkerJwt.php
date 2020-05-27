<?php


namespace Yirius\Admin\widgets;


use Firebase\JWT\JWT;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Claim;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use think\facade\Request;
use Yirius\Admin\admin\login\AdminLogin;
use Yirius\Admin\config\ConsConfig;
use Yirius\Admin\support\abstracts\LoginAbstract;
use Yirius\Admin\support\factory\LoginFactory;
use Yirius\Admin\ThinkerAdmin;

class ThinkerJwt
{
    /**
     * 获取秘钥
     * @return
     */
    private function getSecretKey()
    {
        return ThinkerAdmin::properties()->getJwt('secretKey');
    }

    /**
     * 校验token是否正确
     * @param token
     * @return
     */
    public function verfiy($token){
        try{
            //解析token
            if(!($token instanceof Token)) {
                $token = (new Parser())->parse((string) $token);
            }
            //获取token数据
            $secret = $this->getClaim($token, ConsConfig::$JWT_KEY) . $this->getSecretKey();
            //验证
            $signer = new Sha256();
            //校验时间是否过期
            if($token->verify($signer, $secret)) {
                return !$token->isExpired();
            }
            return false;
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * 获得Token中的信息无需secret解密也能获得
     * @param token
     * @param claim
     * @return
     */
    public function getClaim($token, $claim = null) {
        if(!($token instanceof Token)) {
            $token = (new Parser())->parse((string) $token);
        }
        if(is_null($claim)) {
            return $token->getClaims();
        } else {
            return $token->getClaim($claim, null);
        }
    }

    /**
     * @title      decode
     * @description 重载获取内容
     * @createtime 2020/5/27 12:53 上午
     * @param $token
     * @return array
     * @author     yangyuance
     */
    public function decode($token) {
        if(!($token instanceof Token)) {
            $token = (new Parser())->parse((string) $token);
        }

        //先验证
        if($this->verfiy($token)) {
            //格式化数据
            return array_map(function (Claim $claim){
                return $claim->getValue();
            }, $token->getClaims());
        } else {
            if($token->isExpired()) {
                //如果是过期，判断状态是否操作
                $claims = array_map(function (Claim $claim){
                    return $claim->getValue();
                }, $token->getClaims());

                //找到上次操作时间
                $lastUseTime = ThinkerAdmin::cache()->getTokenCache("jwt", $claims, 0);

                $properties = ThinkerAdmin::properties();

                //判断是否在操作时间内
                if(time() - $lastUseTime <= $properties->getJwt("expiredOperateTime")){
                    //如果还没超过操作时间
                    $lastUseTime = null;

                    unset($claims['exp']);

                    //重新序列化参数
                    $claims[ConsConfig::$JWT_HEADER] = ThinkerAdmin::jwt()->sign($claims);

                    //发送重新注册token的参数
                    ThinkerAdmin::response()
                        ->data($claims)
                        ->msg(lang("authorization has expired"))
                        ->code($properties->getJwt("reExpiredCode"))
                        ->response();
                }
            }

            ThinkerAdmin::response()
                ->msg("当前登录状态已过期，请您重新登录")
                ->code(ThinkerAdmin::properties()->getJwt("expiredCode"))
                ->response();
        }
    }

    /**
     * @title      sign
     * @description 对参数进行加密
     * @createtime 2020/5/27 12:53 上午
     * @param array $data
     * @param null  $useSecretKey
     * @param null  $expiredTime
     * @return string
     * @author     yangyuance
     */
    public function sign(array $data, $useSecretKey = null, $expiredTime = null) {
        try{
            if(is_null($useSecretKey)) {
                $useSecretKey = $this->getSecretKey();
            }
            if(is_null($expiredTime)) {
                $expiredTime = ThinkerAdmin::properties()->getJwt("tokenExpireSeconds");
            }

            //拼接加密秘钥
            $secretKey = $data[ConsConfig::$JWT_KEY] . $useSecretKey;

            $builder = (new Builder())->issuedAt(time())->expiresAt(time() + $expiredTime);

            foreach ($data as $i => $v) {
                $builder->withClaim($i, $v);
            }

            $signer = new Sha256();

            $tokenIns = $builder->getToken($signer, new Key($secretKey));

            return (string) $tokenIns;
        }catch (\Exception $e){
            thinker_error($e);

            ThinkerAdmin::response()->msg("当前生成签名错误，请您联系客服")->fail();
        }
    }

    /**
     * @title getTokenInfo
     * @description 获取到用户的token信息
     * @author YangYuanCe
     * @param request
     * @return {@link Map< String, Object>}
     **/
    public function getTokenInfo($header = null) {
        if(is_null($header)) {
            $header = Request::header(ConsConfig::$JWT_HEADER, null);

            if(empty($header)) {
                $header = Request::param(ConsConfig::$JWT_HEADER, null);
            }
        }

        if(!is_null($header)) {
            return $this->decode($header);
        }

        ThinkerAdmin::response()->msg("无法获取Token")->fail();
    }

    /**
     * @title      getLoginFactory
     * @description 获取到登录对应的工厂方法
     * @createtime 2020/5/27 1:13 上午
     * @param $accessType
     * @return AdminLogin
     * @author     yangyuance
     */
    public function getLoginFactory($accessType) {
        //获取到登录类型
        $loginFactory = new LoginFactory();

        return $loginFactory->loadLogin($loginFactory->loadAccessType(intval($accessType)));
    }

    /**
     * @title verifyPassword
     * @description 验证密码是否正确
     * @author YangYuanCe
     * @param password
     * @return {@link Boolean}
     **/
    public function verifyPassword($password) {
        if(is_null($this->verifyPasswordAndGetUser($password))) {
            return false;
        }
        return true;
    }

    /***
     * @title      verifyPasswordAndGetUser
     * @description 验证密码
     * @createtime 2020/5/27 1:16 上午
     * @param      $password
     * @param null $tokenInfo
     * @return array|null
     * @author     yangyuance
     */
    public function verifyPasswordAndGetUser($password, $tokenInfo = null) {
        if(is_null($tokenInfo)) {
            $tokenInfo = $this->getTokenInfo();
        }

        if(!empty($password)) {
            $loginAbstract = $this->getLoginFactory(
                $tokenInfo[ConsConfig::$JWT_ACCESS_TYPE]
            );

            $user = $loginAbstract->getUser($tokenInfo[ConsConfig::$JWT_KEY]);

            if($loginAbstract->verifyPassword($password, $user)) {
                return $user;
            }

            $user = null;$loginAbstract = null;$tokenInfo = null;
        }

        return null;
    }
}