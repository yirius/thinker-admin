<?php


namespace Yirius\Admin\support\factory;


use think\facade\Request;
use Yirius\Admin\admin\login\AdminLogin;
use Yirius\Admin\config\ConsConfig;
use Yirius\Admin\ThinkerAdmin;

class LoginFactory
{
    protected $authTable = [
        "\\Yirius\\Admin\\admin\\login\\AdminLogin"
    ];

    public function __construct()
    {
        $this->authTable = array_merge($this->authTable, ThinkerAdmin::properties()->getShiro("authTable"));
    }

    /**
     * @title      loadAccessType
     * @description
     * @createtime 2020/5/27 1:09 上午
     * @param null $accessTypeStr
     * @return int
     * @author     yangyuance
     */
    public function loadAccessType($accessTypeStr = null) {
        if(is_null($accessTypeStr)) {
            $accessTypeStr = input("param.".ConsConfig::$JWT_ACCESS_TYPE);
        }

        if(!empty($accessTypeStr)) {
            return intval($accessTypeStr);
        }

        return 0;
    }

    /**
     * @title      loadLogin
     * @description 获取登录对应信息
     * @createtime 2020/5/27 1:12 上午
     * @param null $index
     * @return AdminLogin
     * @author     yangyuance
     */
    public function loadLogin($index = null) {
        if(is_null($index)) {
            $index = $this->loadAccessType();
        }

        $index = intval($index);

        if($index <= count($this->authTable) - 1) {
            try{
                return (new $this->authTable[$index]());
            }catch (\Exception $exception) {
                thinker_error($exception);
            }
        }

        return new AdminLogin();
    }
}